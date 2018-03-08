<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CmsStaging\Test\Unit\Model\Block;

class DataProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testMetadataReplace()
    {
        $metadataProviderMock = $this->getMock(
            'Magento\Staging\Model\Entity\DataProvider\MetadataProvider',
            [],
            [],
            '',
            false
        );
        $collectionFactoryMock = $this->getMock(
            'Magento\Cms\Model\ResourceModel\Block\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->getMock('Magento\Cms\Model\ResourceModel\Block\Collection', [], [], '', false));

        $metadataProviderMock->expects($this->once())->method('getMetadata')->willReturn(['key', 'value']);

        new \Magento\CmsStaging\Model\Block\DataProvider(
            'name',
            'primaryFieldName',
            'requestFieldName',
            $collectionFactoryMock,
            $this->getMock('Magento\Framework\App\Request\DataPersistorInterface'),
            $metadataProviderMock
        );
    }
}
