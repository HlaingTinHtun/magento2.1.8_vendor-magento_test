<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Model\Operation\Update;

use Magento\Staging\Model\Operation\Update\DefaultPermanentUpdateProcessor;

class DefaultPermanentUpdateProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultPermanentUpdateProcessor
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $entityVersionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $updateIntersectedUpdatesMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataPoolMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $objectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $hydratorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $metaDataMock;

    /**
     * @var string
     */
    private $entityType;

    protected function setUp()
    {
        $this->entityType = 'EntityType';
        $this->metaDataMock = $this->getMock(\Magento\Framework\EntityManager\EntityMetadataInterface::class);
        $this->hydratorMock = $this->getMock(\Magento\Framework\EntityManager\HydratorInterface::class);
        $this->objectMock = $this->getMock(\Magento\Framework\Model\AbstractExtensibleModel::class, [], [], '', false);
        $this->entityVersionMock = $this->getMock(
            \Magento\Staging\Model\ResourceModel\Db\ReadEntityVersion::class,
            [],
            [],
            '',
            false
        );
        $this->updateIntersectedUpdatesMock = $this->getMock(
            \Magento\Staging\Model\Operation\Delete\UpdateIntersectedRollbacks::class,
            [],
            [],
            '',
            false
        );
        $this->metadataPoolMock = $this->getMock(
            \Magento\Framework\EntityManager\MetadataPool::class,
            [],
            [],
            '',
            false
        );
        $typeResolverMock = $this->getMock(\Magento\Framework\EntityManager\TypeResolver::class, [], [], '', false);
        $typeResolverMock->expects($this->any())
            ->method('resolve')
            ->willReturn($this->entityType);
        $this->model = new DefaultPermanentUpdateProcessor(
            $typeResolverMock,
            $this->entityVersionMock,
            $this->updateIntersectedUpdatesMock,
            $this->metadataPoolMock
        );
    }

    public function testProcess()
    {
        $versionId = 1;
        $rollbackId = 2;
        $entityData = [
            'id' => 1
        ];
        $nextVersionId = 2;
        $nextPermanentVersionId = 3;
        $this->metadataPoolMock
            ->expects($this->once())
            ->method('getHydrator')
            ->with($this->entityType)
            ->willReturn($this->hydratorMock);
        $this->metadataPoolMock
            ->expects($this->once())
            ->method('getMetadata')
            ->with($this->entityType)
            ->willReturn($this->metaDataMock);
        $this->hydratorMock
            ->expects($this->once())
            ->method('extract')
            ->with($this->objectMock)
            ->willReturn($entityData);
        $this->metaDataMock->expects($this->once())->method('getIdentifierField')->willReturn('id');
        $this->entityVersionMock
            ->expects($this->once())
            ->method('getNextVersionId')
            ->with($this->entityType, $versionId, 1)
            ->willReturn($nextVersionId);
        $this->entityVersionMock
            ->expects($this->once())
            ->method('getNextPermanentVersionId')
            ->with($this->entityType, $versionId, 1)
            ->willReturn($nextPermanentVersionId);
        $this->updateIntersectedUpdatesMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->objectMock, $nextPermanentVersionId);
        $this->model->process($this->objectMock, $versionId, $rollbackId);
    }

    public function testProcessPermanentUpdate()
    {
        $versionId = 1;
        $rollbackId = 2;
        $entityData = [
            'id' => 1
        ];
        $nextVersionId = 2;
        $nextPermanentVersionId = 2;
        $this->metadataPoolMock
            ->expects($this->once())
            ->method('getHydrator')
            ->with($this->entityType)
            ->willReturn($this->hydratorMock);
        $this->metadataPoolMock
            ->expects($this->once())
            ->method('getMetadata')
            ->with($this->entityType)
            ->willReturn($this->metaDataMock);
        $this->hydratorMock
            ->expects($this->once())
            ->method('extract')
            ->with($this->objectMock)
            ->willReturn($entityData);
        $this->metaDataMock->expects($this->once())->method('getIdentifierField')->willReturn('id');
        $this->entityVersionMock
            ->expects($this->once())
            ->method('getNextVersionId')
            ->with($this->entityType, $versionId, 1)
            ->willReturn($nextVersionId);
        $this->entityVersionMock
            ->expects($this->once())
            ->method('getNextPermanentVersionId')
            ->with($this->entityType, $versionId, 1)
            ->willReturn($nextPermanentVersionId);
        $this->updateIntersectedUpdatesMock
            ->expects($this->never())
            ->method('execute');
        $this->model->process($this->objectMock, $versionId, $rollbackId);
    }
}
