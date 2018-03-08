<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedSalesRule\Test\Unit\Model\Rule\Condition\ConcreteCondition\Address;

use Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Address\Region;

/**
 * Class RegionTest
 */
class RegionTest extends \PHPUnit_Framework_TestCase
{
    const CLASS_NAME = 'Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Address\Region';

    const EXPECTED_CLASS_NAME =
        'Magento\AdvancedSalesRule\Model\Rule\Condition\FilterTextGenerator\Address\Region';

    /**
     * @var Region
     */
    protected $model;
    /**
     * @var \Magento\AdvancedRule\Model\Condition\FilterGroupInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterGroupFactory;

    /**
     * @var \Magento\AdvancedRule\Helper\Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterHelper;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Rule\Model\Condition\AbstractCondition|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $abstractCondition;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $className = '\Magento\AdvancedRule\Model\Condition\FilterGroupInterfaceFactory';
        $this->filterGroupFactory = $this->getMock($className, ['create'], [], '', false);

        $className = '\Magento\AdvancedRule\Helper\Filter';
        $this->filterHelper = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Rule\Model\Condition\AbstractCondition';
        $this->abstractCondition = $this->getMockForAbstractClass($className, [], '', false);
    }

    /**
     * test testIsFilterable
     * @param string $attribute
     * @param string $operator
     * @param array|object|null $valueParsed
     * @param bool $expected
     * @dataProvider isFilterableDataProvider
     */
    public function testIsFilterable($attribute, $operator, $valueParsed, $expected)
    {
        $this->abstractCondition->setData('attribute', $attribute);
        $this->abstractCondition->setData('operator', $operator);
        $this->abstractCondition->setData('value_parsed', $valueParsed);

        $this->model = $this->objectManager->getObject(
            self::CLASS_NAME,
            [
                'filterGroupFactory' => $this->filterGroupFactory,
                'filterHelper' => $this->filterHelper,
                'condition' => $this->abstractCondition,
            ]
        );

        $this->assertEquals($expected, $this->model->isFilterable());
    }

    /**
     * @return array
     */
    public function isFilterableDataProvider()
    {
        return [
            'array_val_equal_not_filterable' => ['region', '==', [3], false],
            'obj_val_equal_not_filterable' => ['region', '==', new \stdClass, false],
            'null_val_equal_not_filterable' => ['region', '==', null, false],
            'string_val_equal_filterable' => ['region', '==', 'string', true],

            'array_val_not_equal_not_filterable' => ['region', '!=', [3], false],
            'obj_val_not_equal_not_filterable' => ['region', '!=', new \stdClass, false],
            'null_val_not_equal_not_filterable' => ['region', '!=', null, false],
            'string_val_not_equal_filterable' => ['region', '!=', 'string', true],

            'string_val_greater_equal_not_filterable' => ['region', '>=', 'string', false],
            'array_val_greater_equal_not_filterable' => ['region', '>=', [3], false],
        ];
    }

    /**
     * test GetFilterGroups
     * @param string $operator
     * @dataProvider getFilterGroupsDataProvider
     */
    public function testGetFilterGroups($operator)
    {
        $this->abstractCondition->setData('operator', $operator);
        $this->abstractCondition->setData('attribute', 'address');
        $this->abstractCondition->setData('value_parsed', '1');

        $this->model = $this->objectManager->getObject(
            self::CLASS_NAME,
            [
                'filterGroupFactory' => $this->filterGroupFactory,
                'filterHelper' => $this->filterHelper,
                'condition' => $this->abstractCondition,
            ]
        );

        $className = '\Magento\AdvancedRule\Model\Condition\Filter';
        $filter =
            $this->getMock($className, ['setFilterText', 'setWeight', 'setFilterTextGeneratorClass'], [], '', false);

        //test getFilterTextPrefix
        $filter->expects($this->any())
            ->method('setFilterText')
            ->with('quote_address:address:1')
            ->willReturnSelf();

        $filter->expects($this->any())
            ->method('setWeight')
            ->willReturnSelf();

        //test getFilterTextGeneratorClass
        $filter->expects($this->any())
            ->method('setFilterTextGeneratorClass')
            ->with(self::EXPECTED_CLASS_NAME)
            ->willReturnSelf();

        $className = '\Magento\AdvancedRule\Model\Condition\FilterGroup';
        $filterGroup = $this->getMock($className, [], [], '', false);

        $this->filterHelper->expects($this->once())
            ->method('createFilter')
            ->willReturn($filter);

        $this->filterGroupFactory->expects($this->any())
            ->method('create')
            ->willReturn($filterGroup);

        if ($operator == '==') {
            $return = $this->model->getFilterGroups();
            $this->assertTrue(is_array($return));
            $this->assertSame([$filterGroup], $return);

        } elseif ($operator == '!=') {
            $this->filterHelper->expects($this->any())
                ->method('negateFilter')
                ->with($filter);

            $return = $this->model->getFilterGroups();
            $this->assertTrue(is_array($return));
            $this->assertNotSame([$filterGroup], $return);
        }

        //test caching if (create should be called only once)
        $this->model->getFilterGroups();
    }

    /**
     * @return array
     */
    public function getFilterGroupsDataProvider()
    {
        return [
            'equal' => ['=='],
            'not_equal' => ['!='],
            'greater_than' => ['>=']
        ];
    }
}
