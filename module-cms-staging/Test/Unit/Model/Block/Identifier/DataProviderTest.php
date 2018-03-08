<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CmsStaging\Test\Unit\Model\Block\Identifier;

class DataProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collection;

    /**
     * @var \Magento\CmsStaging\Model\Page\Identifier\DataProvider
     */
    protected $model;

    protected function setUp()
    {
        $this->collection = $this->getMock('Magento\Cms\Model\ResourceModel\Block\Collection', [], [], '', false);
        $collectionFactory = $this->getMock(
            'Magento\Cms\Model\ResourceModel\Block\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $collectionFactory->expects($this->once())->method('create')->willReturn($this->collection);

        $this->model = new \Magento\Cms\Model\Block\DataProvider(
            'name',
            'primaryFieldName',
            'requestFieldName',
            $collectionFactory,
            $this->getMock('Magento\Framework\App\Request\DataPersistorInterface')
        );
    }

    public function testGetData()
    {
        $blockId = 100;
        $blockData = ['key' => 'value'];
        $blockMock = $this->getMock('Magento\Cms\Model\Block', [], [], '', false);
        $blockMock->expects($this->once())->method('getId')->willReturn($blockId);
        $blockMock->expects($this->once())->method('getData')->willReturn($blockData);

        $expectedResult = [$blockId => $blockData];
        $this->collection->expects($this->once())->method('getItems')->willReturn([$blockMock]);

        $this->assertEquals($expectedResult, $this->model->getData());
    }
}
