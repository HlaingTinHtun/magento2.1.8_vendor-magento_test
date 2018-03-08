<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedSalesRule\Test\Unit\Model\Rule\Condition\ConcreteCondition\Address;

use Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Address\Factory;

/**
 * Class FactoryTest
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Address\Factory
     */
    protected $model;
    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerInterface;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\AdvancedSalesRule\Model\Rule\Condition\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressCondition;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $className = '\Magento\Framework\ObjectManagerInterface';
        $this->objectManagerInterface = $this->getMock($className, [], [], '', false);

        $className = '\Magento\AdvancedSalesRule\Model\Rule\Condition\Address';
        $this->addressCondition = $this->getMock(
            $className,
            ['get', 'getAttribute'],
            [],
            '',
            false
        );

        $this->model = $this->objectManager->getObject(
            'Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Address\Factory',
            [
                'objectManager' => $this->objectManagerInterface,
            ]
        );
    }

    /**
     * test Create Default
     * @param string $attribute
     * @dataProvider createDefaultDataProvider
     */
    public function testCreateDefault($attribute)
    {
        $this->addressCondition->expects($this->any())
            ->method('getAttribute')
            ->willReturn($attribute);

        $this->objectManagerInterface->expects($this->any())
            ->method('create')
            ->with('Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\DefaultCondition');

        $this->model->create($this->addressCondition);
    }

    /**
     * @return array
     */
    public function createDefaultDataProvider()
    {
        return [
            'attribute_default' => ['default'],
            'attribute_sku' => ['sku'],
            'attribute_address' => ['address'],
        ];
    }

    /**
     * test Create Default
     * @param string $attribute
     * @param string $class
     * @dataProvider createAddressDataProvider
     */
    public function testCreateAddress($attribute, $class)
    {
        $this->addressCondition->expects($this->any())
            ->method('getAttribute')
            ->willReturn($attribute);

        $this->objectManagerInterface->expects($this->any())
            ->method('create')
            ->with($class, ['condition' => $this->addressCondition]);

        $this->model->create($this->addressCondition);
    }

    /**
     * @return array
     */
    public function createAddressDataProvider()
    {
        return [
            'payment_method' =>
                [
                'payment_method',
                'Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Address\PaymentMethod'
                ],
            'shipping_method' =>
                [
                    'shipping_method',
                    'Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Address\ShippingMethod'
                ],
            'country_id' =>
                [
                    'country_id',
                    'Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Address\CountryId'
                ],
            'region_id' =>
                [
                    'region_id',
                    'Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Address\RegionId'
                ],
            'postcode' =>
                [
                    'postcode',
                    'Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Address\Postcode'
                ],
            'region' =>
                [
                    'region',
                    'Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Address\Region'
                ]
        ];
    }
}
