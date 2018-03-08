<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\PersistentHistory\Test\Unit\Model;

use Magento\Persistent\Observer\EmulateCustomerObserver;

class CustomerEmulatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Persistent\Observer\EmulateCustomerObserver
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistentHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $defaultShippingAddressMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $defaultBillingAddressMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistentSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressRepositoryMock;

    protected function setUp()
    {
        $this->persistentHelperMock = $this->getMock('Magento\Persistent\Helper\Session', [], [], '', false);
        $this->helperMock = $this->getMock('Magento\Persistent\Helper\Data', [], [], '', false);
        $sessionMethods = [
            'setDefaultTaxShippingAddress',
            'setDefaultTaxBillingAddress',
            'setCustomerId',
            'setCustomerGroupId',
            'setIsCustomerEmulated',
            'isLoggedIn'
        ];
        $this->customerSessionMock = $this->getMock('Magento\Customer\Model\Session', $sessionMethods, [], '', false);
        $this->customerRepositoryMock = $this->getMock('Magento\Customer\Api\CustomerRepositoryInterface');
        $methods = ['getCountryId', 'getRegion', 'getRegionId', 'getPostcode'];
        $this->defaultShippingAddressMock = $this->getMock('Magento\Customer\Model\Address', $methods, [], '', false);
        $this->defaultBillingAddressMock = $this->getMock('Magento\Customer\Model\Address', $methods, [], '', false);
        $this->observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $this->customerMock = $this->getMock('Magento\Customer\Api\Data\CustomerInterface');
        $this->persistentSessionMock = $this->getMock(
            'Magento\Persistent\Model\Session',
            ['getCustomerId'],
            [],
            '',
            false
        );
        $this->addressRepositoryMock = $this->getMock('Magento\Customer\Api\AddressRepositoryInterface');
        $this->model = new EmulateCustomerObserver(
            $this->persistentHelperMock,
            $this->helperMock,
            $this->customerSessionMock,
            $this->customerRepositoryMock,
            $this->addressRepositoryMock
        );
    }

    public function testExecuteWhenCannotProcessPersistentData()
    {
        $this->helperMock->expects($this->once())->method('canProcess')->with($this->observerMock)->willReturn(false);
        $this->helperMock->expects($this->never())->method('isShoppingCartPersist');
        $this->model->execute($this->observerMock);
    }

    public function testExecuteWhenShoppingCartNotPersisted()
    {
        $this->helperMock->expects($this->once())->method('canProcess')->with($this->observerMock)->willReturn(true);
        $this->helperMock->expects($this->once())->method('isShoppingCartPersist')->willReturn(false);
        $this->model->execute($this->observerMock);
    }

    public function testExecuteWhenCustomerLoggedIn()
    {
        $this->helperMock->expects($this->once())->method('canProcess')->with($this->observerMock)->willReturn(true);
        $this->helperMock->expects($this->once())->method('isShoppingCartPersist')->willReturn(true);
        $this->persistentHelperMock->expects($this->once())->method('isPersistent')->willReturn(true);
        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->willReturn(true);
        $this->customerRepositoryMock->expects($this->never())->method('getById');
        $this->model->execute($this->observerMock);
    }

    public function testExecuteWhenCustomerHasAddresses()
    {
        $customerId = 1;
        $countryId = 3;
        $regionId = 4;
        $postcode = 90210;
        $customerGroupId = 2;
        $this->helperMock->expects($this->once())->method('canProcess')->with($this->observerMock)->willReturn(true);
        $this->helperMock->expects($this->once())->method('isShoppingCartPersist')->willReturn(true);
        $this->persistentHelperMock->expects($this->once())->method('isPersistent')->willReturn(true);
        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->willReturn(false);
        $this->persistentHelperMock
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->persistentSessionMock);
        $this->persistentSessionMock->expects($this->once())->method('getCustomerId')->willReturn($customerId);
        $this->customerRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($this->customerMock);
        $this->customerMock
            ->expects($this->once())
            ->method('getDefaultShipping')
            ->willReturn('shippingId');
        $this->customerMock
            ->expects($this->once())
            ->method('getDefaultBilling')
            ->willReturn('billingId');
        $valueMap = [
            ['shippingId', $this->defaultShippingAddressMock],
            ['billingId', $this->defaultBillingAddressMock]
        ];
        $this->addressRepositoryMock->expects($this->any())->method('getById')->willReturnMap($valueMap);
        $this->defaultBillingAddressMock->expects($this->once())->method('getCountryId')->willReturn($countryId);
        $this->defaultBillingAddressMock->expects($this->once())->method('getRegion')->willReturn('California');
        $this->defaultBillingAddressMock->expects($this->once())->method('getRegionId')->willReturn($regionId);
        $this->defaultBillingAddressMock->expects($this->once())->method('getPostcode')->willReturn($postcode);
        $this->defaultShippingAddressMock->expects($this->once())->method('getCountryId')->willReturn($countryId);
        $this->defaultShippingAddressMock->expects($this->once())->method('getRegion')->willReturn('California');
        $this->defaultShippingAddressMock->expects($this->once())->method('getRegionId')->willReturn($regionId);
        $this->defaultShippingAddressMock->expects($this->once())->method('getPostcode')->willReturn($postcode);
        $this->customerSessionMock
            ->expects($this->once())
            ->method('setDefaultTaxShippingAddress')
            ->with(
                [
                    'country_id' => $countryId,
                    'region_id' => $regionId,
                    'postcode' => $postcode
                ]
            );
        $this->customerSessionMock
            ->expects($this->once())
            ->method('setDefaultTaxBillingAddress')
            ->with(
                [
                    'country_id' => $countryId,
                    'region_id' => $regionId,
                    'postcode' => $postcode
                ]
            );
        $this->customerMock->expects($this->once())->method('getId')->willReturn($customerId);
        $this->customerMock->expects($this->once())->method('getGroupId')->willReturn($customerGroupId);
        $this->customerSessionMock
            ->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->willReturnSelf();
        $this->customerSessionMock
            ->expects($this->once())
            ->method('setCustomerGroupId')
            ->with($customerGroupId)
            ->willReturnSelf();
        $this->model->execute($this->observerMock);
    }

    public function testExecuteWhenCustomerDoesnotHaveAddress()
    {
        $customerId = 1;
        $customerGroupId = 2;
        $this->helperMock->expects($this->once())->method('canProcess')->with($this->observerMock)->willReturn(true);
        $this->helperMock->expects($this->once())->method('isShoppingCartPersist')->willReturn(true);
        $this->persistentHelperMock->expects($this->once())->method('isPersistent')->willReturn(true);
        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->willReturn(false);
        $this->persistentHelperMock
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->persistentSessionMock);
        $this->persistentSessionMock->expects($this->once())->method('getCustomerId')->willReturn($customerId);
        $this->customerRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($this->customerMock);
        $this->customerMock
            ->expects($this->once())
            ->method('getDefaultShipping')
            ->willReturn(null);
        $this->customerMock
            ->expects($this->once())
            ->method('getDefaultBilling')
            ->willReturn(null);
        $this->customerSessionMock
            ->expects($this->never())
            ->method('setDefaultTaxShippingAddress');
        $this->customerSessionMock
            ->expects($this->never())
            ->method('setDefaultTaxBillingAddress');
        $this->customerMock->expects($this->once())->method('getId')->willReturn($customerId);
        $this->customerMock->expects($this->once())->method('getGroupId')->willReturn($customerGroupId);
        $this->customerSessionMock
            ->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->willReturnSelf();
        $this->customerSessionMock
            ->expects($this->once())
            ->method('setCustomerGroupId')
            ->with($customerGroupId)
            ->will($this->returnSelf());
        $this->model->execute($this->observerMock);
    }
}
