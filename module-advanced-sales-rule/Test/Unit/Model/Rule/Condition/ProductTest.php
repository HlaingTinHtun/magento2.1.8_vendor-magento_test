<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedSalesRule\Test\Unit\Model\Rule\Condition;

use Magento\AdvancedSalesRule\Model\Rule\Condition\Product;
use Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Product\Factory;

/**
 * Class ProductTest
 */
class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedSalesRule\Model\Rule\Condition\Product
     */
    protected $model;
    /**
     * @var \Magento\Rule\Model\Condition\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendData;

    /**
     * @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * @var \Magento\Catalog\Model\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productResource;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrSetCollection;

    /**
     * @var \Magento\Framework\Locale\FormatInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeFormat;

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

        $className = '\Magento\Backend\Helper\Data';
        $this->backendData = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Eav\Model\Config';
        $this->config = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Catalog\Model\ProductFactory';
        $this->productFactory = $this->getMock($className, ['create'], [], '', false);

        $className = '\Magento\Catalog\Api\ProductRepositoryInterface';
        $this->productRepository = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Catalog\Model\ResourceModel\Product';
        $this->productResource = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Eav\Model\Entity\AbstractEntity';
        $abstractEntity = $this->getMock($className, [], [], '', false);

        $this->productResource->expects($this->any())
              ->method('loadAllAttributes')
              ->willReturn($abstractEntity);

        $abstractEntity->expects($this->any())
            ->method('getAttributesByCode')
            ->willReturn([]);

        $className = '\Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection';
        $this->attrSetCollection = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Framework\Locale\FormatInterface';
        $this->localeFormat = $this->getMock($className, [], [], '', false);

        $className = '\Magento\AdvancedSalesRule\Model\Rule\Condition\ConcreteCondition\Product\Factory';
        $this->concreteConditionFactory = $this->getMock($className, ['create'], [], '', false);

        $this->model = $this->objectManager->getObject(
            'Magento\AdvancedSalesRule\Model\Rule\Condition\Product',
            [
                'context' => $this->context,
                'backendData' => $this->backendData,
                'config' => $this->config,
                'productFactory' => $this->productFactory,
                'productRepository' => $this->productRepository,
                'productResource' => $this->productResource,
                'attrSetCollection' => $this->attrSetCollection,
                'localeFormat' => $this->localeFormat,
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
