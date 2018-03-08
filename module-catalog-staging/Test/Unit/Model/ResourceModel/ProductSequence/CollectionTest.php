<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogStaging\Test\Unit\Model\ResourceModel\ProductSequence;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testDeleteSequence()
    {
        $objectManager = new ObjectManager($this);
        $metadataPoolMock = $this->getMock(\Magento\Framework\EntityManager\MetadataPool::class, [], [], '', false);
        $resourceMock = $this->getMock(\Magento\Framework\App\ResourceConnection::class, [], [], '', false);
        $sequenceRegistryMock = $this->getMock(
            \Magento\Framework\EntityManager\Sequence\SequenceRegistry::class,
            [],
            [],
            '',
            false
        );
        $metadataMock = $this->getMock(\Magento\Framework\EntityManager\EntityMetadata::class, [], [], '', false);
        $metadataPoolMock->expects($this->any())
            ->method('getMetadata')
            ->willReturn($metadataMock);
        $connectionMock = $this->getMock(\Magento\Framework\DB\Adapter\AdapterInterface::class, [], [], '', false);
        $sequenceRegistryMock->expects($this->once())
            ->method('retrieve')
            ->willReturn(['sequenceTable' => 'sequence_table']);
        $metadataMock->expects($this->once())
            ->method('getEntityConnection')
            ->willReturn($connectionMock);
        /** @var \Magento\CatalogStaging\Model\ResourceModel\ProductSequence\Collection $model */
        $model = $objectManager->getObject(
            \Magento\CatalogStaging\Model\ResourceModel\ProductSequence\Collection::class,
            [
                'metadataPool' => $metadataPoolMock,
                'resource' => $resourceMock,
                'sequenceRegistry' => $sequenceRegistryMock
            ]
        );
        $resourceMock->expects($this->once())
            ->method('getTableName')
            ->with('sequence_table')
            ->willReturn('sequence_table');
        $ids = [1, 2, 3];
        $connectionMock->expects($this->once())
            ->method('delete')
            ->with('sequence_table', ['sequence_value IN (?)' => $ids]);
        $model->deleteSequence($ids);
    }
}
