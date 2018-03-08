<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Model\Operation\Update;

use Magento\Staging\Model\ResourceModel\Db\ReadEntityVersion;
use Magento\Staging\Model\Operation\Delete\UpdateIntersectedRollbacks;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\TypeResolver;

class DefaultPermanentUpdateProcessor implements \Magento\Staging\Model\Operation\Update\UpdateProcessorInterface
{
    /**
     * @var TypeResolver
     */
    private $typeResolver;

    /**
     * @var ReadEntityVersion
     */
    private $entityVersion;

    /**
     * @var UpdateIntersectedRollbacks
     */
    private $updateIntersectedUpdates;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param TypeResolver $typeResolver
     * @param ReadEntityVersion $entityVersion
     * @param UpdateIntersectedRollbacks $updateIntersectedUpdates
     * @param MetadataPool $metadataPool
     */
    public function __construct(
        TypeResolver $typeResolver,
        ReadEntityVersion $entityVersion,
        UpdateIntersectedRollbacks $updateIntersectedUpdates,
        MetadataPool $metadataPool
    ) {
        $this->typeResolver = $typeResolver;
        $this->entityVersion = $entityVersion;
        $this->updateIntersectedUpdates = $updateIntersectedUpdates;
        $this->metadataPool = $metadataPool;
    }

    /**
     * {@inheritdoc}
     */
    public function process($entity, $versionId, $rollbackId = null)
    {
        $entityType = $this->typeResolver->resolve($entity);
        $hydrator = $this->metadataPool->getHydrator($entityType);
        $metadata = $this->metadataPool->getMetadata($entityType);
        $entityData = $hydrator->extract($entity);
        $entityId = $entityData[$metadata->getIdentifierField()];

        $nextVersionId = $this->entityVersion->getNextVersionId($entityType, $versionId, $entityId);
        $nextPermanentVersionId = $this->entityVersion->getNextPermanentVersionId($entityType, $versionId, $entityId);

        if ($nextVersionId !== $nextPermanentVersionId) {
            $this->updateIntersectedUpdates->execute($entity, $nextPermanentVersionId);
        }
    }
}
