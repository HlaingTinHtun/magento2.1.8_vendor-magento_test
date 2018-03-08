<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogStaging\Test\Unit\Model\Product\Identifier;

class DataProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogStaging\Model\Product\Identifier\DataProvider
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $poolMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    protected function setUp()
    {
        $this->collectionMock = $this->getMock(
            'Magento\Catalog\Model\ResourceModel\Product\Collection',
            [],
            [],
            '',
            false
        );
        $collectionFactoryMock = $this->getMock(
            'Magento\Catalog\Model\ResourceModel\Product\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $collectionFactoryMock->expects($this->once())->method('create')->willReturn($this->collectionMock);
        $this->poolMock = $this->getMock('Magento\Ui\DataProvider\Modifier\PoolInterface');
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->model = new \Magento\CatalogStaging\Model\Product\Identifier\DataProvider(
            'name',
            'primaryFieldName',
            'requestFieldName',
            $collectionFactoryMock,
            $this->poolMock,
            $this->requestMock
        );
    }

    public function testGetData()
    {
        $productId = 100;
        $productName = 'name';
        $storeId = 1;
        $productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $productMock->expects($this->exactly(2))->method('getId')->willReturn($productId);
        $productMock->expects($this->once())->method('getName')->willReturn($productName);

        $this->collectionMock->expects($this->once())->method('getItems')->willReturn([$productMock]);
        $this->requestMock
            ->expects($this->once())
            ->method('getParam')
            ->with('store', \Magento\Store\Model\Store::DEFAULT_STORE_ID)
            ->willReturn($storeId);

        $expectedResult = [
            $productId => [
                'entity_id' => $productId,
                'name' => $productName,
                'store_id' => $storeId
            ]
        ];

        $this->assertEquals($expectedResult, $this->model->getData());
    }
}
