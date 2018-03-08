<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedSalesRule\Test\Unit\Model\Rule\Condition;

use Magento\AdvancedSalesRule\Model\Rule\Condition\Address;
use Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Address\Factory;

/**
 * Class AddressTest
 */
class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedSalesRule\Model\Rule\Condition\Address
     */
    protected $model;
    /**
     * @var \Magento\Rule\Model\Condition\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Directory\Model\Config\Source\Country|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryCountry;

    /**
     * @var \Magento\Directory\Model\Config\Source\Allregion|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryAllregion;

    /**
     * @var \Magento\Shipping\Model\Config\Source\Allmethods|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shippingAllmethods;

    /**
     * @var \Magento\Payment\Model\Config\Source\Allmethods|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $paymentAllmethods;

    /**
     * @var Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $concreteConditionFactory;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $className = '\Magento\Rule\Model\Condition\Context';
        $this->context = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Directory\Model\Config\Source\Country';
        $this->directoryCountry = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Directory\Model\Config\Source\Allregion';
        $this->directoryAllregion = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Shipping\Model\Config\Source\Allmethods';
        $this->shippingAllmethods = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Payment\Model\Config\Source\Allmethods';
        $this->paymentAllmethods = $this->getMock($className, [], [], '', false);

        $className = '\Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Address\Factory';
        $this->concreteConditionFactory = $this->getMock($className, ['create'], [], '', false);

        $this->model = $this->objectManager->getObject(
            'Magento\AdvancedSalesRule\Model\Rule\Condition\Address',
            [
                'context' => $this->context,
                'directoryCountry' => $this->directoryCountry,
                'directoryAllregion' => $this->directoryAllregion,
                'shippingAllmethods' => $this->shippingAllmethods,
                'paymentAllmethods' => $this->paymentAllmethods,
                'concreteConditionFactory' => $this->concreteConditionFactory,
                'data' => [],
            ]
        );
    }

    /**
     * test IsFilterable
     */
    public function testIsFilterable()
    {
        $className = 'Magento\AdvancedRule\Model\Condition\FilterableConditionInterface';
        $interface =$this->getMock($className, [], [], '', false);

        $interface->expects($this->any())
            ->method('isFilterable')
            ->willReturn(true);

        $this->concreteConditionFactory->expects($this->any())
            ->method('create')
            ->willReturn($interface);

        $this->assertTrue($this->model->isFilterable());
    }

    /**
     * test GetFilterGroups
     */
    public function testGetFilterGroups()
    {
        $className = 'Magento\AdvancedRule\Model\Condition\FilterGroupInterface';
        $filterGroupInterface =$this->getMock($className, [], [], '', false);

        $className = 'Magento\AdvancedRule\Model\Condition\FilterableConditionInterface';
        $interface =$this->getMock($className, [], [], '', false);

        $interface->expects($this->any())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupInterface]);

        $this->concreteConditionFactory->expects($this->any())
            ->method('create')
            ->willReturn($interface);

        $this->assertEquals([$filterGroupInterface], $this->model->getFilterGroups());
    }
}
