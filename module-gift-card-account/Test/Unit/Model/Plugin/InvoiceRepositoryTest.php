<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCardAccount\Test\Unit\Model\Plugin;


class InvoiceRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCardAccount\Model\Plugin\InvoiceRepository
     */
    private $plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $subjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $invoiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributeMock;

    protected function setUp()
    {
        $this->subjectMock = $this->getMock(
            \Magento\Sales\Model\Order\InvoiceRepository::class,
            [],
            [],
            '',
            false
        );
        $methods = ['getExtensionAttributes', 'setGiftCardsAmount', 'setBaseGiftCardsAmount'];
        $this->invoiceMock = $this->getMock(\Magento\Sales\Model\Order\Invoice::class, $methods, [], '', false);
        $this->extensionAttributeMock = $this->getMock(
            \Magento\Sales\Api\Data\InvoiceExtension::class,
            ['getGiftCardsAmount', 'getBaseGiftCardsAmount'],
            [],
            '',
            false
        );
        $this->plugin = new \Magento\GiftCardAccount\Model\Plugin\InvoiceRepository();
    }

    public function testBeforeSave()
    {
        $this->invoiceMock
            ->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($this->extensionAttributeMock);
        $this->extensionAttributeMock->expects($this->once())->method('getGiftCardsAmount')->willReturn(10);
        $this->extensionAttributeMock->expects($this->once())->method('getBaseGiftCardsAmount')->willReturn(15);
        $this->invoiceMock->expects($this->once())->method('setGiftCardsAmount')->with(10)->willReturnSelf();
        $this->invoiceMock->expects($this->once())->method('setBaseGiftCardsAmount')->with(15)->willReturnSelf();
        $this->plugin->beforeSave($this->subjectMock, $this->invoiceMock);
    }
}
