<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Model\ResourceModel\Db;

use Magento\Staging\Model\VersionManager;
use Magento\Staging\Api\Data\UpdateInterface;

/**
 * Class CampaignValidator
 */
class CampaignValidator
{
    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Magento\Framework\EntityManager\TypeResolver
     */
    private $typeResolver;

    /**
     * @var \Magento\Framework\EntityManager\HydratorPool
     */
    private $hydratorPool;

    /**
     * @var \Magento\Staging\Api\UpdateRepositoryInterface
     */
    private $updateRepository;

    /**
     * @var \Magento\Staging\Model\StagingList
     */
    private $stagingList;

    /**
     * CampaignValidator constructor.
     *
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Framework\EntityManager\HydratorPool $hydratorPool
     * @param \Magento\Framework\EntityManager\TypeResolver $typeResolver
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Staging\Api\UpdateRepositoryInterface $updateRepository
     * @param \Magento\Staging\Model\StagingList|null $stagingList
     */
    public function __construct(
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\EntityManager\HydratorPool $hydratorPool,
        \Magento\Framework\EntityManager\TypeResolver $typeResolver,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Staging\Api\UpdateRepositoryInterface $updateRepository,
        \Magento\Staging\Model\StagingList $stagingList = null
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
        $this->typeResolver = $typeResolver;
        $this->hydratorPool = $hydratorPool;
        $this->updateRepository = $updateRepository;
        $this->stagingList = $stagingList ?:
            \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Staging\Model\StagingList::class);
    }

    /**
     * Checks if Update can be changed.
     *
     * Checks for existing in the future of the company which have
     * intersection with the current company or update. Returns false if intersections was found
     * or true if intersections was not found.
     *
     * @param UpdateInterface $update
     * @param int $updateEndTime
     * @return bool
     */
    public function canBeUpdated(UpdateInterface $update, $updateEndTime)
    {
        $connection = $this->resourceConnection->getConnection();
        $selects = [];
        foreach ($this->stagingList->getEntityTypes() as $entityType) {
            $metadata = $this->metadataPool->getMetadata($entityType);
            $subSelect = $connection->select()
                ->from([$metadata->getEntityTable()], [$metadata->getIdentifierField()])
                ->where('created_in = ?', $update->getId())
                ->setPart('disable_staging_preview', true);
            $selects[] = $connection->select()
                ->from($metadata->getEntityTable(), ['COUNT(*) AS cnt'])
                ->where('created_in > ?', $update->getRollbackId())
                ->where('created_in <= ?', $updateEndTime)
                ->where($metadata->getIdentifierField() . ' IN(?)', $subSelect)
                ->setPart('disable_staging_preview', true);
        }

        $derivedSelect = $connection->select()->union($selects, \Magento\Framework\DB\Select::SQL_UNION_ALL);
        $select = $connection->select()
            ->from($derivedSelect, ['IF(SUM(cnt) > 0, 0, 1)']);
        return (bool) $connection->fetchOne($select);
    }

    /**
     * @param object $entity
     * @param string $version
     * @param string|null $previousVersion
     * @return bool
     */
    public function canBeScheduled($entity, $version, $previousVersion = null)
    {
        $previousDates = [];
        if ($previousVersion) {
            $previous = $this->updateRepository->get($previousVersion);
            $previousDates = [$previous->getId(), $previous->getRollbackId()];
        }
        $update = $this->updateRepository->get($version);
        $entityType = $this->typeResolver->resolve($entity);
        $metadata = $this->metadataPool->getMetadata($entityType);
        $hydrator = $this->hydratorPool->getHydrator($entityType);
        $entityData = $hydrator->extract($entity);
        $identifier = $entityData[$metadata->getIdentifierField()];
        $connection = $this->resourceConnection->getConnectionByName($metadata->getEntityConnectionName());
        $select = $connection->select()
            ->from(['t' => $metadata->getEntityTable()], [$metadata->getLinkField()])
            ->where('t.' . $metadata->getIdentifierField() . ' = ?', $identifier)
            ->where('t.created_in = ?', $update->getId())
            ->setPart('disable_staging_preview', true);
        $rowId = $connection->fetchOne($select);

        $intersections = $this->getIntersectingVersions(
            $entityType,
            $identifier,
            $update->getId(),
            $update->getRollbackId(),
            $rowId
        );
        return (count(array_diff($intersections, $previousDates)) <= 0);
    }

    /**
     * Find all temporary updates intersected with custom period
     *
     * @param string $entityType
     * @param int $identifier
     * @param int $createdIn
     * @param int $updatedIn
     * @param int|null $rowId
     * @return bool|array
     */
    private function getIntersectingVersions($entityType, $identifier, $createdIn, $updatedIn, $rowId = null)
    {
        //to provide ability install entity data
        if (!$createdIn) {
            return [];
        }
        //permanent update is not considered as intersecting update
        if (!$updatedIn || $updatedIn == VersionManager::MAX_VERSION) {
            //need to verify that it start version isn't included into temporary update
            $condition[] = sprintf(
                '(e.created_in <= %s AND e.updated_in > %s AND s.rollback_id IS NOT NULL)',
                $createdIn,
                $createdIn
            );
            //created_in date aren't match for permanents campaigns
            $condition[] = sprintf('(e.created_in = %s AND s.rollback_id IS NULL)', $createdIn);
            return $this->verifyIsUpdateMatched($entityType, $identifier, $rowId, $condition);
        }
        //verifications for temporary campaigns
        // need to verify that start date isn't included into other temporary update
        $condition[] = sprintf(
            '(e.created_in between %s AND %s AND s.is_rollback IS NULL)',
            $createdIn,
            $updatedIn
        );
        $condition[] = sprintf(
            '(e.created_in <= %s AND e.updated_in > %s AND s.rollback_id IS NOT NULL)',
            $createdIn,
            $createdIn
        );
        $isMatched = $this->verifyIsUpdateMatched($entityType, $identifier, $rowId, $condition);
        if ($isMatched) {
            return $isMatched;
        }
        $condition = [];
        // need to verify that our temporary dates aren't included into other temporary update
        $condition[] = sprintf('(e.created_in > %s AND e.updated_in < %s)', $createdIn, $updatedIn);
        //updated in isn't a part of temporary update
        $condition[] = sprintf('(e.created_in < %s AND e.updated_in >= %s)', $updatedIn, $updatedIn);
        $isMatched = $this->verifyIsUpdateMatched($entityType, $identifier, $rowId, $condition, true);
        if ($isMatched) {
            return $isMatched;
        }
        // verify temporary update start time in existed update
        $condition = [];
        $condition[] = sprintf('(e.updated_in between %s AND %s)', $createdIn, $updatedIn);
        $condition[] = sprintf('(e.updated_in < %s)', $updatedIn);
        return $this->verifyIsUpdateInherited($entityType, $identifier, $condition);
    }

    /**
     * @param string $entityType
     * @param int $identifier
     * @param int|null $rowId
     * @param string|array $condition
     * @param bool $verifyForTemporary
     * @return array
     */
    private function verifyIsUpdateMatched($entityType, $identifier, $rowId, $condition, $verifyForTemporary = false)
    {
        $metadata = $this->metadataPool->getMetadata($entityType);
        $connection = $this->resourceConnection->getConnectionByName($metadata->getEntityConnectionName());
        $select = $connection->select()->reset()
            ->from(['e' => $metadata->getEntityTable()], ['e.created_in'])
            ->joinLeft(
                ['s' => $this->resourceConnection->getTableName('staging_update')],
                'e.created_in = s.id',
                []
            )
            ->where($metadata->getIdentifierField() . ' = ?', $identifier)
            ->where(implode(' OR ', $condition))
            ->setPart('disable_staging_preview', true);
        if ($verifyForTemporary) {
            $select->where('s.rollback_id IS NOT NULL');
        }
        if ($rowId) {
            $select->where($metadata->getLinkField() . ' != ?', $rowId);
        }
        return $connection->fetchCol($select);
    }

    /**
     * @param string $entityType
     * @param int $identifier
     * @param array $condition
     * @return array
     */
    private function verifyIsUpdateInherited($entityType, $identifier, $condition)
    {
        $metadata = $this->metadataPool->getMetadata($entityType);
        $connection = $this->resourceConnection->getConnectionByName($metadata->getEntityConnectionName());
        $select = $connection->select()->reset()
            ->from(['e' => $metadata->getEntityTable()], ['e.created_in'])
            ->joinLeft(
                ['s' => $this->resourceConnection->getTableName('staging_update')],
                'e.created_in = s.id',
                []
            )
            ->where($metadata->getIdentifierField() . ' = ?', $identifier)
            ->where(implode(' AND ', $condition))
            ->setPart('disable_staging_preview', true);

        $select->where('s.rollback_id IS NOT NULL');
        return $connection->fetchCol($select);
    }
}
