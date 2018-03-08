<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogRuleStaging\Test\Unit\Pricing\Price;

use Magento\CatalogRuleStaging\Pricing\Price\CatalogRulePrice;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CatalogRulePriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogRuleStaging\Pricing\Price\CatalogRulePrice
     */
    protected $price;

    /**
     * @var \Magento\Framework\Pricing\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableItemMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataTimeMock;

    /**
     * @var \Magento\Store\Model\StoreManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\Framework\Pricing\PriceInfo\Base | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfoMock;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\RuleFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogRuleResourceFactoryMock;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogRuleResourceMock;

    /**
     * @var \Magento\Store\Model\Website|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreStoreMock;

    /**
     * @var \Magento\Framework\Pricing\Adjustment\Calculator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $calculator;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceCurrencyMock;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Staging\Model\VersionManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $versionManagerMock;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface| \PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogRepositoryMock;

    protected function setUp()
    {
        $this->saleableItemMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getPrice', 'getId', '__wakeup', 'getPriceInfo', 'getParentId'],
            [],
            '',
            false
        );
        $this->dataTimeMock = $this->getMockForAbstractClass(
            'Magento\Framework\Stdlib\DateTime\TimezoneInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );

        $this->coreStoreMock = $this->getMock('\Magento\Store\Api\Data\StoreInterface');
        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', [], [], '', false);
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->coreStoreMock));

        $this->customerSessionMock = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);
        $this->priceInfoMock = $this->getMock(
            '\Magento\Framework\Pricing\PriceInfo',
            ['getAdjustments'],
            [],
            '',
            false
        );
        $this->catalogRuleResourceFactoryMock = $this->getMock(
            '\Magento\CatalogRule\Model\ResourceModel\RuleFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->catalogRuleResourceMock = $this->getMock(
            '\Magento\CatalogRule\Model\ResourceModel\Rule',
            [],
            [],
            '',
            false
        );

        $this->priceInfoMock->expects($this->any())
            ->method('getAdjustments')
            ->will($this->returnValue([]));
        $this->saleableItemMock->expects($this->any())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));

        $this->catalogRuleResourceFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->catalogRuleResourceMock));

        $this->calculator = $this->getMockBuilder('Magento\Framework\Pricing\Adjustment\Calculator')
            ->disableOriginalConstructor()
            ->getMock();
        $qty = 1;

        $this->priceCurrencyMock = $this->getMock('\Magento\Framework\Pricing\PriceCurrencyInterface');
        $this->collectionFactory =
            $this->getMock('Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory', ['create'], [], '', false);

        $this->versionManagerMock = $this->getMock('Magento\Staging\Model\VersionManager', [], [], '', false);
        $this->catalogRepositoryMock = $this->getMock('Magento\Catalog\Api\ProductRepositoryInterface');
        $this->price = new CatalogRulePrice(
            $this->saleableItemMock,
            $qty,
            $this->calculator,
            $this->priceCurrencyMock,
            $this->dataTimeMock,
            $this->storeManagerMock,
            $this->customerSessionMock,
            $this->catalogRuleResourceFactoryMock,
            $this->collectionFactory,
            $this->versionManagerMock,
            $this->catalogRepositoryMock
        );
    }

    /**
     * @dataProvider getValueDataProvider
     */
    public function testGetValue($simpleAction, $expectedPrice)
    {
        $price = 10;
        $websiteId = 1;
        $customerGroupId = 2;
        $ruleCollection =
            $this->getMock('Magento\CatalogRule\Model\ResourceModel\Rule\Collection', [], [], '', false);
        $ruleMock = $this->getMock('Magento\CatalogRule\Model\Rule', [], [], '', false);
        $this->versionManagerMock->expects($this->once())->method('isPreviewVersion')->willReturn(true);
        $this->saleableItemMock->expects($this->once())->method('getPrice')->willReturn($price);
        $this->coreStoreMock->expects($this->once())->method('getWebsiteId')->willReturn($websiteId);
        $this->customerSessionMock->expects($this->once())->method('getCustomerGroupId')->willReturn($customerGroupId);
        $this->collectionFactory->expects($this->once())->method('create')->willReturn($ruleCollection);
        $ruleCollection->expects($this->once())->method('addWebsiteFilter')->with($websiteId)->willReturnSelf();
        $this->saleableItemMock->expects($this->once())->method('getParentId')->willReturn(false);
        $this->catalogRepositoryMock->expects($this->never())->method('getById');
        $ruleCollection
            ->expects($this->once())
            ->method('addCustomerGroupFilter')
            ->with($customerGroupId)
            ->willReturnSelf();
        $ruleCollection->expects($this->once())->method('addFieldToFilter')->with('is_active', 1)->willReturnSelf();
        $ruleCollection
            ->expects($this->once())
            ->method('setOrder')
            ->with('sort_order', \Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ->willReturn([$ruleMock]);
        $ruleMock->expects($this->once())->method('validate')->with($this->saleableItemMock)->willReturn(true);
        $ruleMock->expects($this->once())->method('getSimpleAction')->willReturn($simpleAction);
        $ruleMock->expects($this->once())->method('getDiscountAmount')->willReturn(5);
        $this->priceCurrencyMock->expects($this->once())->method('round')->willReturn($expectedPrice);
        $ruleMock->expects($this->once())->method('getStopRulesProcessing')->willReturn(true);
        $this->assertEquals($expectedPrice, $this->price->getValue());
    }

    public function getValueDataProvider()
    {
        return [
            ['to_fixed', 5],
            ['to_percent', 0.5],
            ['by_fixed', 5],
            ['by_percent', 9.5]
        ];
    }


    public function testGetValueForDefaultPrice()
    {
        $price = 10;
        $websiteId = 1;
        $customerGroupId = 2;
        $expectedPrice = 0;
        $ruleCollection =
            $this->getMock('Magento\CatalogRule\Model\ResourceModel\Rule\Collection', [], [], '', false);
        $ruleMock = $this->getMock('Magento\CatalogRule\Model\Rule', [], [], '', false);
        $this->versionManagerMock->expects($this->once())->method('isPreviewVersion')->willReturn(true);
        $this->saleableItemMock->expects($this->once())->method('getPrice')->willReturn($price);
        $this->coreStoreMock->expects($this->once())->method('getWebsiteId')->willReturn($websiteId);
        $this->customerSessionMock->expects($this->once())->method('getCustomerGroupId')->willReturn($customerGroupId);
        $this->collectionFactory->expects($this->once())->method('create')->willReturn($ruleCollection);
        $ruleCollection->expects($this->once())->method('addWebsiteFilter')->with($websiteId)->willReturnSelf();
        $ruleCollection
            ->expects($this->once())
            ->method('addCustomerGroupFilter')
            ->with($customerGroupId)
            ->willReturnSelf();
        $ruleCollection->expects($this->once())->method('addFieldToFilter')->with('is_active', 1)->willReturnSelf();
        $ruleCollection
            ->expects($this->once())
            ->method('setOrder')
            ->with('sort_order', \Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ->willReturn([$ruleMock]);
        $ruleMock->expects($this->once())->method('validate')->with($this->saleableItemMock)->willReturn(true);
        $ruleMock->expects($this->once())->method('getSimpleAction')->willReturn('default');
        $ruleMock->expects($this->never())->method('getDiscountAmount');
        $this->priceCurrencyMock->expects($this->once())->method('round')->willReturn($expectedPrice);
        $ruleMock->expects($this->once())->method('getStopRulesProcessing')->willReturn(false);
        $this->assertEquals($expectedPrice, $this->price->getValue());
    }

    public function testGetValueForInvalidProduct()
    {
        $price = 10;
        $websiteId = 1;
        $customerGroupId = 2;
        $ruleCollection =
            $this->getMock('Magento\CatalogRule\Model\ResourceModel\Rule\Collection', [], [], '', false);
        $ruleMock = $this->getMock('Magento\CatalogRule\Model\Rule', [], [], '', false);
        $this->versionManagerMock->expects($this->once())->method('isPreviewVersion')->willReturn(true);
        $this->saleableItemMock->expects($this->once())->method('getPrice')->willReturn($price);
        $this->coreStoreMock->expects($this->once())->method('getWebsiteId')->willReturn($websiteId);
        $this->customerSessionMock->expects($this->once())->method('getCustomerGroupId')->willReturn($customerGroupId);
        $this->collectionFactory->expects($this->once())->method('create')->willReturn($ruleCollection);
        $ruleCollection->expects($this->once())->method('addWebsiteFilter')->with($websiteId)->willReturnSelf();
        $ruleCollection
            ->expects($this->once())
            ->method('addCustomerGroupFilter')
            ->with($customerGroupId)
            ->willReturnSelf();
        $ruleCollection->expects($this->once())->method('addFieldToFilter')->with('is_active', 1)->willReturnSelf();
        $ruleCollection
            ->expects($this->once())
            ->method('setOrder')
            ->with('sort_order', \Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ->willReturn([$ruleMock]);
        $ruleMock->expects($this->once())->method('validate')->with($this->saleableItemMock)->willReturn(false);
        $ruleMock->expects($this->never())->method('getSimpleAction')->willReturn('default');
        $ruleMock->expects($this->never())->method('getDiscountAmount');
        $this->priceCurrencyMock->expects($this->never())->method('round');
        $ruleMock->expects($this->never())->method('getStopRulesProcessing');
        $this->assertEquals($price, $this->price->getValue());
    }

    public function testGetValueForConfigurableProduct()
    {
        $price = 10;
        $websiteId = 1;
        $customerGroupId = 2;
        $ruleCollection =
            $this->getMock('Magento\CatalogRule\Model\ResourceModel\Rule\Collection', [], [], '', false);
        $saleableItem = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getPrice', 'getId', '__wakeup', 'getPriceInfo', 'getParentId'],
            [],
            '',
            false
        );
        $ruleMock = $this->getMock('Magento\CatalogRule\Model\Rule', [], [], '', false);
        $this->versionManagerMock->expects($this->once())->method('isPreviewVersion')->willReturn(true);
        $this->saleableItemMock->expects($this->once())->method('getPrice')->willReturn($price);
        $this->coreStoreMock->expects($this->once())->method('getWebsiteId')->willReturn($websiteId);
        $this->customerSessionMock->expects($this->once())->method('getCustomerGroupId')->willReturn($customerGroupId);
        $this->collectionFactory->expects($this->once())->method('create')->willReturn($ruleCollection);
        $ruleCollection->expects($this->once())->method('addWebsiteFilter')->with($websiteId)->willReturnSelf();
        $this->saleableItemMock->expects($this->exactly(2))->method('getParentId')->willReturn(2);
        $this->catalogRepositoryMock->expects($this->once())->method('getById')->with(2)->willReturn($saleableItem);
        $ruleCollection
            ->expects($this->once())
            ->method('addCustomerGroupFilter')
            ->with($customerGroupId)
            ->willReturnSelf();
        $ruleCollection->expects($this->once())->method('addFieldToFilter')->with('is_active', 1)->willReturnSelf();
        $ruleCollection
            ->expects($this->once())
            ->method('setOrder')
            ->with('sort_order', \Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ->willReturn([$ruleMock]);
        $ruleMock->expects($this->once())->method('validate')->with($saleableItem)->willReturn(true);
        $ruleMock->expects($this->once())->method('getSimpleAction')->willReturn('to_fixed');
        $ruleMock->expects($this->once())->method('getDiscountAmount')->willReturn(5);
        $this->priceCurrencyMock->expects($this->once())->method('round')->willReturn(5);
        $ruleMock->expects($this->once())->method('getStopRulesProcessing')->willReturn(true);
        $this->assertEquals(5, $this->price->getValue());
    }
}
