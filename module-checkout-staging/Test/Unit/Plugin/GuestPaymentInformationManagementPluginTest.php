<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CheckoutStaging\Test\Unit\Plugin;

use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Magento\CheckoutStaging\Plugin\GuestPaymentInformationManagementPlugin;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Staging\Model\VersionManager;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class GuestPaymentInformationManagementPluginTest
 */
class GuestPaymentInformationManagementPluginTest extends \PHPUnit_Framework_TestCase
{
    const CART_ID = 1;
    const EMAIL = 'test@test.com';

    /**
     * @var VersionManager|MockObject
     */
    private $versionManager;

    /**
     * @var GuestPaymentInformationManagementInterface|MockObject
     */
    private $paymentInformationManagement;

    /**
     * @var PaymentInterface|MockObject
     */
    private $paymentMethod;

    /**
     * @var AddressInterface|MockObject
     */
    private $address;

    /**
     * @var GuestPaymentInformationManagementPlugin
     */
    private $plugin;

    protected function setUp()
    {
        $this->versionManager = $this->getMockBuilder(VersionManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['isPreviewVersion'])
            ->getMock();

        $this->paymentInformationManagement = $this->getMock(GuestPaymentInformationManagementInterface::class);
        $this->paymentMethod = $this->getMock(PaymentInterface::class);
        $this->address = $this->getMock(AddressInterface::class);

        $this->plugin = new GuestPaymentInformationManagementPlugin($this->versionManager);
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Preview mode doesn't allow submitting the order.
     */
    public function testBeforeSavePaymentInformationAndPlaceOrder()
    {
        $this->versionManager->expects(static::once())
            ->method('isPreviewVersion')
            ->willReturn(true);

        $this->plugin->beforeSavePaymentInformationAndPlaceOrder(
            $this->paymentInformationManagement,
            self::CART_ID,
            self::EMAIL,
            $this->paymentMethod,
            $this->address
        );
    }
}
