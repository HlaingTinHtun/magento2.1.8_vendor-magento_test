<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerBalance\Test\Unit\Observer;

use Magento\CustomerBalance\Observer\PaymentDataImportObserver;

class PaymentDataImportObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerBalance\Observer\PaymentDataImportObserver
     */
    protected $observer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerBalanceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $paymentMock;

    protected function setUp()
    {
        $custBalanceMethods = ['setCustomerId', 'setWebsiteId', 'loadByCustomer'];
        $this->customerBalanceMock = $this->getMock(
            'Magento\CustomerBalance\Model\Balance',
            $custBalanceMethods,
            [],
            '',
            false
        );
        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $quoteMethods = ['getCustomerId', 'getStoreId', 'getIsMultiShipping', 'setUseCustomerBalance',
            'setCustomerBalanceInstance'];
        $this->quoteMock = $this->getMock('Magento\Quote\Model\Quote', $quoteMethods, [], '', false);
        $this->observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $eventMethods = ['getPayment', 'getInput'];
        $this->eventMock = $this->getMock('Magento\Framework\Event', $eventMethods, [], '', false);
        $this->paymentMock = $this->getMock('Magento\Quote\Model\Quote\Payment', [], [], '', false);
        $this->observer = new PaymentDataImportObserver(
            $this->customerBalanceMock,
            $this->storeManagerMock
        );
    }

    public function testExecuteForNotMultishippingQuote()
    {
        $this->observerMock->expects($this->once())->method('getEvent')->willReturn($this->eventMock);
        $this->eventMock->expects($this->once())->method('getPayment')->willReturn($this->paymentMock);
        $this->paymentMock->expects($this->once())->method('getQuote')->willReturn($this->quoteMock);
        $this->quoteMock->expects($this->once())->method('getIsMultiShipping')->willReturn(false);
        $this->eventMock->expects($this->never())->method('getInput');
        $this->observer->execute($this->observerMock);
    }

    public function testExecuteForMultishippingQuote()
    {
        $storeId = 1;
        $customerId = 2;
        $storeMock = $this->getMock('Magento\Store\Api\Data\StoreInterface');
        $inputMock = $this->getMock(
            'Magento\Framework\DataObject',
            ['getUseCustomerBalance', 'getMethod', 'setMethod'],
            [],
            '',
            false
        );
        $this->observerMock->expects($this->once())->method('getEvent')->willReturn($this->eventMock);
        $this->eventMock->expects($this->once())->method('getPayment')->willReturn($this->paymentMock);
        $this->paymentMock->expects($this->once())->method('getQuote')->willReturn($this->quoteMock);
        $this->quoteMock->expects($this->once())->method('getIsMultiShipping')->willReturn(true);
        $this->eventMock->expects($this->once())->method('getInput')->willReturn($inputMock);
        $inputMock->expects($this->once())->method('getUseCustomerBalance')->willReturn(true);
        $this->quoteMock->expects($this->once())->method('getStoreId')->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())->method('getStore')->with($storeId)->willReturn($storeMock);
        $this->quoteMock->expects($this->once())->method('getCustomerId')->willReturn($customerId);
        $this->quoteMock->expects($this->once())->method('setUseCustomerBalance')->willReturn(true);
        $this->customerBalanceMock
            ->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->willReturnSelf();
        $storeMock->expects($this->once())->method('getWebsiteId')->willReturn(2);
        $this->customerBalanceMock->expects($this->once())->method('setWebsiteId')->with(2)->willReturnSelf();
        $this->customerBalanceMock->expects($this->once())->method('loadByCustomer')->willReturnSelf();
        $this->quoteMock
            ->expects($this->once())
            ->method('setCustomerBalanceInstance')
            ->with($this->customerBalanceMock);
        $inputMock->expects($this->once())->method('getMethod')->willReturn(null);
        $inputMock->expects($this->once())->method('setMethod')->with('free');
        $this->observer->execute($this->observerMock);

    }
}
