<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CmsStaging\Test\Unit\Model\Page\Identifier;

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
        $this->collection = $this->getMock('Magento\Cms\Model\ResourceModel\Page\Collection', [], [], '', false);
        $collectionFactory = $this->getMock(
            'Magento\Cms\Model\ResourceModel\Page\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $collectionFactory->expects($this->once())->method('create')->willReturn($this->collection);

        $this->model = new \Magento\CmsStaging\Model\Page\Identifier\DataProvider(
            'name',
            'primaryFieldName',
            'requestFieldName',
            $collectionFactory,
            $this->getMock('Magento\Framework\App\Request\DataPersistorInterface')
        );
    }

    public function testGetData()
    {
        $pageId = 100;
        $pageTitle = 'title';
        $pageMock = $this->getMock('Magento\Cms\Model\Page', [], [], '', false);
        $pageMock->expects($this->exactly(2))->method('getId')->willReturn($pageId);
        $pageMock->expects($this->once())->method('getTitle')->willReturn($pageTitle);

        $expectedResult = [
            $pageId => [
                'page_id' => $pageId,
                'title' => $pageTitle
            ]
        ];
        $this->collection->expects($this->once())->method('getItems')->willReturn([$pageMock]);

        $this->assertEquals($expectedResult, $this->model->getData());
    }
}
