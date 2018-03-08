<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCard\Test\Unit\Model\Product;

use Magento\Catalog\Api\Data\ProductExtension;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\GiftCard\Model\Catalog\Product\Type\Giftcard;
use Magento\GiftCard\Model\Product\SaveHandler;
use Magento\GiftCard\Model\ResourceModel\Db\GetAmountIdsByProduct;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class SaveHandlerTest @covers \Magento\GiftCard\Model\Product\SaveHandler
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * SaveHandler instance holder.
     *
     * @var SaveHandler
     */
    private $saveHandler;

    /**
     * MetadataPool mock holder.
     *
     * @var MetadataPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataPool;

    /**
     * StoreManager mock holder.
     *
     * @var StoreManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManager;

    /**
     * GetAmountIdsByProduct mock holder.
     *
     * @var GetAmountIdsByProduct|\PHPUnit_Framework_MockObject_MockObject
     */
    private $getAmountIdsByProduct;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->metadataPool = $this->getMockBuilder(MetadataPool::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManager = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->getAmountIdsByProduct = $this->getMockBuilder(GetAmountIdsByProduct::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->saveHandler = $objectManager->getObject(
            SaveHandler::class,
            [
                'metadataPool' => $this->metadataPool,
                'storeManager' => $this->storeManager,
                'getAmountIdsByProduct' => $this->getAmountIdsByProduct
            ]
        );
    }

    /**
     * Test gift card save.
     *
     * @covers SaveHandler::execute()
     * @return void
     */
    public function testExecute()
    {
        $giftCardAmounts = ['test' => []];
        $entityData = ['row_id' => 1];
        $storeMock = $this->getMockBuilder(\Magento\Store\Api\Data\StoreInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(1);
        $metadataMock = $this->getMockBuilder(\Magento\Framework\EntityManager\EntityMetadataInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $metadataMock->expects($this->once())
            ->method('getLinkField')
            ->willReturn('row_id');
        $extensionAttributes = $this->getMockBuilder(ProductExtension::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGiftcardAmounts'])
            ->getMock();
        $extensionAttributes->expects($this->once())
            ->method('getGiftcardAmounts')
            ->willReturn([]);
        $productMock = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getTypeId')->willReturn(Giftcard::TYPE_GIFTCARD);
        $productMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($extensionAttributes);
        $productMock->expects($this->once())
            ->method('getData')
            ->with('giftcard_amounts')
            ->willReturn($giftCardAmounts);
        $hydratorMock = $this->getMockBuilder(\Magento\Framework\EntityManager\HydratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $hydratorMock->expects($this->once())
            ->method('extract')
            ->with($productMock)
            ->willReturn($entityData);
        $this->metadataPool->expects($this->once())->method('getMetadata')->willReturn($metadataMock);
        $this->metadataPool->expects($this->once())
            ->method('getHydrator')
            ->with(ProductInterface::class)
            ->willReturn($hydratorMock);
        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $this->getAmountIdsByProduct->expects($this->once())
            ->method('execute')
            ->with('row_id', 1, 1)
            ->willReturn([]);
        $this->saveHandler->execute($productMock);
    }
}
