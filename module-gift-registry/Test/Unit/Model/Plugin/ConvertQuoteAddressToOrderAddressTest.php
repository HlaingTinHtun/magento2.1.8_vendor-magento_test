<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Test\Unit\Model\Plugin;

class ConvertQuoteAddressToOrderAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Model\Plugin\ConvertQuoteAddressToOrderAddress
     */
    protected $model;

    protected function setUp()
    {
        $this->model = new \Magento\GiftRegistry\Model\Plugin\ConvertQuoteAddressToOrderAddress();
    }

    public function testAroundConvertIfAddressFromGiftRegistry()
    {
        $giftRegistryId = 100;
        $quoteAddressMock = $this->getMock(
            '\Magento\Quote\Model\Quote\Address',
            ['getGiftregistryItemId'],
            [],
            '',
            false
        );
        $data = ['some' => 'data'];
        $orderMock = $this->getMock('\Magento\Sales\Model\Order\Address', ['setGiftregistryItemId'], [], '', false);
        $subjectMock = $this->getMock('\Magento\Quote\Model\Quote\Address\ToOrderAddress', [], [], '', false);

        $closure = function ($quoteAddressMock, $data) use ($orderMock) {
            return $orderMock;
        };

        $quoteAddressMock->expects($this->exactly(2))->method('getGiftregistryItemId')->willReturn($giftRegistryId);
        $orderMock->expects($this->once())->method('setGiftregistryItemId')->with($giftRegistryId);

        $this->assertEquals(
            $orderMock,
            $this->model->aroundConvert($subjectMock, $closure, $quoteAddressMock, $data)
        );
    }

    public function testAroundConvertIfAddressNotFromGiftRegistry()
    {
        $quoteAddressMock = $this->getMock(
            '\Magento\Quote\Model\Quote\Address',
            ['getGiftregistryItemId'],
            [],
            '',
            false
        );
        $data = ['some' => 'data'];
        $orderMock = $this->getMock('\Magento\Sales\Model\Order\Address', ['setGiftregistryItemId'], [], '', false);
        $subjectMock = $this->getMock('\Magento\Quote\Model\Quote\Address\ToOrderAddress', [], [], '', false);

        $closure = function ($quoteAddressMock, $data) use ($orderMock) {
            return $orderMock;
        };

        $quoteAddressMock->expects($this->once())->method('getGiftregistryItemId')->willReturn(null);

        $this->assertEquals(
            $orderMock,
            $this->model->aroundConvert($subjectMock, $closure, $quoteAddressMock, $data)
        );
    }
}
