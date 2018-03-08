<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Model\Operation\Update;

use Magento\Staging\Model\Operation\Update\CreateEntityVersion;

class CreateEntityVersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataPoolMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $createMainMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $createExtensionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $createRelationMock;

    /**
     * @var CreateEntityVersion
     */
    private $model;

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
        $this->metadataPoolMock = $this->getMock(
            \Magento\Framework\EntityManager\MetadataPool::class,
            [],
            [],
            '',
            false
        );
        $this->metaDataMock = $this->getMock(\Magento\Framework\EntityManager\EntityMetadataInterface::class);
        $this->hydratorMock = $this->getMock(\Magento\Framework\EntityManager\HydratorInterface::class);
        $this->objectMock = $this->getMock(
            \Magento\Framework\Model\AbstractExtensibleModel::class,
            [],
            [],
            '',
            false
        );
        $this->createMainMock = $this->getMock(
            \Magento\Framework\EntityManager\Operation\Create\CreateMain::class,
            [],
            [],
            '',
            false
        );
        $this->createRelationMock = $this->getMock(
            \Magento\Framework\EntityManager\Operation\Create\CreateExtensions::class,
            [],
            [],
            '',
            false
        );
        $this->createExtensionMock = $this->getMock(
            \Magento\Framework\EntityManager\Operation\Create\CreateAttributes::class,
            [],
            [],
            '',
            false
        );
        $typeResolverMock = $this->getMock(\Magento\Framework\EntityManager\TypeResolver::class, [], [], '', false);
        $typeResolverMock->expects($this->any())
            ->method('resolve')
            ->willReturn($this->entityType);
        $this->model = new CreateEntityVersion(
            $typeResolverMock,
            $this->metadataPoolMock,
            $this->createMainMock,
            $this->createRelationMock,
            $this->createExtensionMock
        );
    }

    public function testExecute()
    {
        $createdIn = 1;
        $updatedIn = 2;
        $entityData = [
            'linkedField' => 1
        ];
        $arguments = [
            'created_in' => $createdIn,
            'updated_in' => $updatedIn
        ];
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
        $this->metaDataMock->expects($this->once())->method('getLinkField')->willReturn('linkedField');
        $this->hydratorMock
            ->expects($this->once())
            ->method('hydrate')
            ->with($this->objectMock, ['linkedField' => null])
            ->willReturn($this->objectMock);
        $this->createMainMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->objectMock, $arguments);
        $this->createExtensionMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->objectMock, $arguments);
        $this->createRelationMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->objectMock, $arguments);
        $arguments = [
            'created_in' => $createdIn,
            'updated_in' => $updatedIn
        ];
        $this->model->execute($this->objectMock, $arguments);
    }
}
