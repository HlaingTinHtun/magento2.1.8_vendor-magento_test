<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Model\Entity\Update\Action\Save;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class SaveAction creates new Update or edits existing Update by data from request
 *
 * Update is scheduled change of a Magento store entities.
 *
 * @see http://devdocs.magento.com/guides/v2.1/mrg/ee/Staging.html
 */
class SaveAction implements \Magento\Staging\Model\Entity\Update\Action\ActionInterface
{
    /**
     * @var \Magento\Staging\Controller\Adminhtml\Entity\Update\Service
     * @deprecated Since functionality of this service was implemented in SaveAction class
     */
    private $updateService;

    /**
     * @var \Magento\Staging\Model\VersionManager
     */
    private $versionManager;

    /**
     * @var \Magento\Staging\Model\Entity\HydratorInterface
     */
    private $entityHydrator;

    /**
     * @var \Magento\Staging\Model\EntityStaging
     */
    private $entityStaging;

    /**
     * @var \Magento\Staging\Api\UpdateRepositoryInterface
     */
    private $updateRepository;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;

    /**
     * @var \Magento\Staging\Model\UpdateFactory
     */
    private $updateFactory;

    /**
     * @var \Magento\Staging\Model\Update\UpdateValidator
     */
    private $validator;

    /**
     * SaveAction constructor.
     *
     * @param \Magento\Staging\Controller\Adminhtml\Entity\Update\Service $updateService
     * @param \Magento\Staging\Model\VersionManager $versionManager
     * @param \Magento\Staging\Model\Entity\HydratorInterface $entityHydrator
     * @param \Magento\Staging\Model\EntityStaging $entityStaging
     * @param \Magento\Staging\Api\UpdateRepositoryInterface|null $updateRepository
     * @param \Magento\Framework\EntityManager\MetadataPool|null $metadataPool
     * @param \Magento\Staging\Model\UpdateFactory|null $updateFactory
     * @param \Magento\Staging\Model\Update\UpdateValidator|null $validator
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct(
        \Magento\Staging\Controller\Adminhtml\Entity\Update\Service $updateService,
        \Magento\Staging\Model\VersionManager $versionManager,
        \Magento\Staging\Model\Entity\HydratorInterface $entityHydrator,
        \Magento\Staging\Model\EntityStaging $entityStaging,
        \Magento\Staging\Api\UpdateRepositoryInterface $updateRepository = null,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool = null,
        \Magento\Staging\Model\UpdateFactory $updateFactory = null,
        \Magento\Staging\Model\Update\UpdateValidator $validator = null
    ) {
        $this->updateService = $updateService;
        $this->versionManager = $versionManager;
        $this->entityHydrator = $entityHydrator;
        $this->entityStaging = $entityStaging;
        $this->updateRepository = $updateRepository ?:
            \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Staging\Api\UpdateRepositoryInterface::class);
        $this->metadataPool = $metadataPool ?:
            \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\EntityManager\MetadataPool::class);
        $this->updateFactory = $updateFactory ?:
            \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Staging\Model\UpdateFactory::class);
        $this->validator = $validator ?:
            \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Staging\Model\Update\UpdateValidator::class);
    }

    /**
     * {@inheritdoc}
     * @param array $params
     * @return bool
     * @throws LocalizedException
     */
    public function execute(array $params)
    {
        $this->validator->validateParams($params);
        $stagingData = $params['stagingData'];
        $entityData = $params['entityData'];

        if (!isset($stagingData['update_id']) || empty($stagingData['update_id'])) {
            $update = $this->createUpdate($stagingData);
            $this->versionManager->setCurrentVersionId($update->getId());

            $this->schedule($entityData, $update->getId());
        } else {
            $update = $this->updateRepository->get($stagingData['update_id']);
            $this->versionManager->setCurrentVersionId($update->getId());

            $update = $this->editUpdate($stagingData);

            $this->schedule(
                $entityData,
                $update->getId(),
                [
                    'origin_in' => $stagingData['update_id'],
                ]
            );
        }

        return true;
    }

    /**
     * Validate input parameters
     *
     * @param array $params
     * @return void
     * @deprecated Since functionality was moved
     * @see \Magento\Staging\Model\Update\Validator
     */
    protected function validateParams(array $params)
    {
        $this->validator->validateParams($params);
    }

    /**
     * Create new update with data from request
     *
     * @param array $stagingData
     * @return \Magento\Staging\Model\Update
     */
    private function createUpdate(array $stagingData)
    {
        /** @var \Magento\Staging\Model\Update $update */
        $update = $this->updateFactory->create();
        $hydrator = $this->metadataPool->getHydrator(\Magento\Staging\Api\Data\UpdateInterface::class);
        $hydrator->hydrate($update, $stagingData);

        $update->setIsCampaign(false);

        $this->updateRepository->save($update);

        return $update;
    }

    /**
     * Edit existing update with data from request
     *
     * Before editing update executes checks:
     *   - If update start time value was changed, then decline editing.
     *   - If update already started, then decline editing.
     *
     * @param array $stagingData
     * @return \Magento\Staging\Api\Data\UpdateInterface
     * @throws LocalizedException if incorrect changes of datetime attribute was detected
     */
    private function editUpdate(array $stagingData)
    {
        $update = $this->updateRepository->get($stagingData['update_id']);
        $dataStart = strtotime($stagingData['start_time']);
        $updateStart = strtotime($update->getStartTime());

        if ($dataStart != $updateStart) {
            unset($stagingData['update_id']);
            return $this->createUpdate($stagingData);
        }

        $this->validator->validateUpdateStarted($update, $stagingData);

        $hydrator = $this->metadataPool->getHydrator(\Magento\Staging\Api\Data\UpdateInterface::class);
        $hydrator->hydrate($update, $stagingData);
        $this->updateRepository->save($update);

        return $update;
    }

    /**
     * Set schedule for requested entity
     *
     * @param array $entityData
     * @param int $version
     * @param array $arguments
     * @return void
     */
    private function schedule(array $entityData, $version, array $arguments = [])
    {
        $entity = $this->entityHydrator->hydrate($entityData);

        $this->entityStaging->schedule($entity, $version, $arguments);
    }
}
