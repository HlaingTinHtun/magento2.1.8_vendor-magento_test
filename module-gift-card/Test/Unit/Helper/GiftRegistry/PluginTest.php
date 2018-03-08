<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCard\Test\Unit\Helper\GiftRegistry;

use Magento\GiftCard\Helper\GiftRegistry\Plugin;
use Magento\GiftCard\Model\Catalog\Product\Type\Giftcard as ProductType;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productRepositoryMock;

    /**
     * @var \Magento\GiftCard\Helper\GiftRegistry\Plugin
     */
    protected $plugin;

    protected function setUp()
    {
        $this->productRepositoryMock = $this->getMock(
            'Magento\Catalog\Api\ProductRepositoryInterface',
            [],
            [],
            '',
            false
        );
        $this->plugin = new Plugin($this->productRepositoryMock);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testAroundCanAddToGiftRegistryPhysicalCard()
    {
        $productId = 333222555;
        $subjectMock = $this->getMock('Magento\GiftRegistry\Helper\Data', [], [], '', false);
        /**
         * @SuppressWarnings(PHPMD.UnusedFormalParameter)
         * @param mixed $param
         * @return bool
         */
        $proceed = function ($param) {
            return true;
        };
        $itemMock = $this->getMock(
            'Magento\Quote\Model\Quote\Item',
            ['getProductType', 'getProductId'],
            [],
            '',
            false
        );
        $itemMock->expects($this->once())->method('getProductType')->willReturn(ProductType::TYPE_GIFTCARD);
        $itemMock->expects($this->once())->method('getProductId')->willReturn($productId);
        $productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);
        $typeMock = $this->getMock('Magento\GiftCard\Model\Catalog\Product\Type\Giftcard', [], [], '', false);
        $productMock->expects($this->once())->method('getTypeInstance')->willReturn($typeMock);
        $typeMock->expects($this->once())->method('isTypePhysical')->willReturn(true);
        $result = $this->plugin->aroundCanAddToGiftRegistry($subjectMock, $proceed, $itemMock);
        $this->assertEquals(true, $result);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testAroundCanAddToGiftRegistryVirtualCard()
    {
        $subjectMock = $this->getMock('Magento\GiftRegistry\Helper\Data', [], [], '', false);
        /**
         * @SuppressWarnings(PHPMD.UnusedFormalParameter)
         * @param mixed $param
         * @return bool
         */
        $proceed = function ($param) {
            return false;
        };
        $itemMock = $this->getMock(
            'Magento\Quote\Model\Quote\Item',
            ['getProductType', 'getProductId'],
            [],
            '',
            false
        );
        $itemMock->expects($this->never())->method('getProductType');
        $itemMock->expects($this->never())->method('getTypeId');
        $result = $this->plugin->aroundCanAddToGiftRegistry($subjectMock, $proceed, $itemMock);
        $this->assertEquals(false, $result);
    }
}
