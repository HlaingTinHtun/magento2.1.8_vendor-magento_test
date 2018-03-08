<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedSalesRule\Test\Unit\Model\Rule\Condition\ConcreteCondition\Product;

use Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Product\Factory;

/**
 * Class FactoryTest
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Product\Factory
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
     * @var \Magento\AdvancedSalesRule\Model\Rule\Condition\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productCondition;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $className = '\Magento\Framework\ObjectManagerInterface';
        $this->objectManagerInterface = $this->getMock($className, [], [], '', false);

        $className = '\Magento\AdvancedSalesRule\Model\Rule\Condition\Product';
        $this->productCondition = $this->getMock(
            $className,
            ['get', 'getOperator', 'getAttribute', 'getValueParsed'],
            [],
            '',
            false
        );

        $this->model = $this->objectManager->getObject(
            'Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Product\Factory',
            [
                'objectManager' => $this->objectManagerInterface,
            ]
        );
    }

    /**
     * test Create Category
     */
    public function testCreateCategory()
    {
        $this->productCondition->expects($this->any())
            ->method('getAttribute')
            ->willReturn('category_ids');

        $this->productCondition->expects($this->any())
            ->method('getOperator')
            ->willReturn('==');

        $this->productCondition->expects($this->any())
            ->method('getValueParsed')
            ->willReturn([3, 4, 5]);

        $this->objectManagerInterface->expects($this->any())
            ->method('create')
            ->with(
                'Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Product\Categories',
                $this->arrayHasKey('data')
            );

        $this->model->create($this->productCondition);
    }

    /**
     * test Create Default
     * @param string $attribute
     * @dataProvider createDefaultDataProvider
     */
    public function testCreateDefault($attribute)
    {
        $this->productCondition->expects($this->any())
            ->method('getAttribute')
            ->willReturn($attribute);

        $this->productCondition->expects($this->any())
            ->method('getOperator')
            ->willReturn('==');

        $this->objectManagerInterface->expects($this->any())
            ->method('create')
            ->with('Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\DefaultCondition');

        $this->model->create($this->productCondition);
    }

    /**
     * @return array
     */
    public function createDefaultDataProvider()
    {
        return [
            'quote_item_qty' => ['quote_item_qty'],
            'quote_item_price' => ['quote_item_price'],
            'quote_item_row_total' => ['quote_item_row_total'],
        ];
    }

    /**
     * test Create Attribute
     */
    public function testCreateAttribute()
    {
        $this->productCondition->expects($this->any())
            ->method('getAttribute')
            ->willReturn('sku');

        $this->productCondition->expects($this->any())
            ->method('getOperator')
            ->willReturn('==');

        $this->objectManagerInterface->expects($this->any())
            ->method('create')
            ->with(
                'Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Product\Attribute',
                ['condition' => $this->productCondition]
            );

        $this->model->create($this->productCondition);
    }
}
