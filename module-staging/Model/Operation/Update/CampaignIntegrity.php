<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Staging\Model\Operation\Update;

use Magento\Framework\EntityManager\TypeResolver;
use Magento\Staging\Api\Data\UpdateInterface;

/**
 * Class CampaignIntegrity
 */
class CampaignIntegrity
{
    /**
     * @var PermanentUpdateProcessorPool
     */
    private $permanentUpdateProcessorPool;

    /**
     * @var TemporaryUpdateProcessorPool
     */
    private $temporaryUpdateProcessorPool;

    /**
     * @var TypeResolver
     */
    private $typeResolver;

    /**
     * CampaignIntegrity constructor.
     *
     * @param PermanentUpdateProcessorPool $permanentUpdateProcessorPool
     * @param TemporaryUpdateProcessorPool $temporaryUpdateProcessorPool
     * @param TypeResolver $typeResolver
     */
    public function __construct(
        PermanentUpdateProcessorPool $permanentUpdateProcessorPool,
        TemporaryUpdateProcessorPool $temporaryUpdateProcessorPool,
        TypeResolver $typeResolver
    ) {
        $this->permanentUpdateProcessorPool = $permanentUpdateProcessorPool;
        $this->temporaryUpdateProcessorPool = $temporaryUpdateProcessorPool;
        $this->typeResolver = $typeResolver;
    }

    /**
     * @param UpdateInterface $update
     * @param object $entity
     * @throws \Exception
     */
    public function synchronizeAffectedCampaigns(UpdateInterface $update, $entity)
    {
        $entityType = $this->typeResolver->resolve($entity);
        if (!$update->getRollbackId()) {
            $processor = $this->permanentUpdateProcessorPool->getProcessor($entityType);
            $processor->process($entity, $update->getId());
        }
    }

    /**
     * @param UpdateInterface $update
     * @param object $entity
     * @throws \Exception
     */
    public function createRollbackPoint(UpdateInterface $update, $entity)
    {
        $entityType = $this->typeResolver->resolve($entity);
        if ($update->getRollbackId()) {
            $processor = $this->temporaryUpdateProcessorPool->getProcessor($entityType);
            $processor->process($entity, $update->getId(), $update->getRollbackId());
        }
    }
}
