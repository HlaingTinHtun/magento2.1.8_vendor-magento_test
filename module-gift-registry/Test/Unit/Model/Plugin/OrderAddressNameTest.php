<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Test\Unit\Model\Plugin;

class OrderAddressNameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Model\Plugin\OrderAddressName
     */
    protected $model;

    protected function setUp()
    {
        $this->model = new \Magento\GiftRegistry\Model\Plugin\OrderAddressName();
    }

    public function testAroundGetNameIfAddressFromGiftRegistry()
    {
        $subjectMock = $this->getMock('\Magento\Sales\Model\Order\Address', ['getGiftregistryItemId'], [], '', false);
        $subjectMock->expects($this->once())->method('getGiftregistryItemId')->willReturn(100);

        $closure = function () {
            return true;
        };

        $this->assertEquals(
            __("Ship to the recipient's address."),
            $this->model->aroundGetName($subjectMock, $closure)
        );
    }

    public function testAroundGetNameIfAddressNotFromGiftRegistry()
    {
        $addressName = __("some name");
        $subjectMock = $this->getMock('\Magento\Sales\Model\Order\Address', ['getGiftregistryItemId'], [], '', false);
        $subjectMock->expects($this->once())->method('getGiftregistryItemId')->willReturn(null);

        $closure = function () use ($addressName) {
            return $addressName;
        };

        $this->assertEquals($addressName, $this->model->aroundGetName($subjectMock, $closure));
    }
}
