<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\PricePermissions\Test\Unit\Observer;

class AdminhtmlBlockHtmlBeforeObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PricePermissions\Observer\AdminhtmlBlockHtmlBeforeObserver
     */
    protected $_observer;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    protected $_varienObserver;

    /**
     * @var \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected $_block;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\PricePermissions\Observer\ObserverData|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerData;

    protected function setUp()
    {
        $this->_registry = $this->getMock(
            'Magento\Framework\Registry',
            ['registry'],
            [],
            '',
            false
        );
        $this->_request = $this->getMock(
            'Magento\Framework\App\RequestInterface',
            [],
            [],
            '',
            false,
            false
        );
        $this->_storeManager = $this->getMock(
            'Magento\Store\Model\StoreManagerInterface',
            [],
            [],
            '',
            false,
            false
        );

        $this->observerData = $this->getMock(
            'Magento\PricePermissions\Observer\ObserverData',
            [],
            [],
            '',
            false
        );
        $this->observerData->expects($this->any())->method('isCanEditProductPrice')->willReturn(false);
        $this->observerData->expects($this->any())->method('isCanReadProductPrice')->willReturn(false);
        $this->observerData->expects($this->any())->method('canEditProductStatus')->willReturn(false);
        $this->observerData->expects($this->any())->method('getDefaultProductPriceString')->willReturn('default');

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $constructArguments = $objectManager->getConstructArguments(
            'Magento\PricePermissions\Observer\AdminhtmlBlockHtmlBeforeObserver',
            [
                'coreRegistry' => $this->_registry,
                'request' => $this->_request,
                'storeManager' => $this->_storeManager,
                'observerData' => $this->observerData,
            ]
        );

        $this->_observer = $this->getMock(
            'Magento\PricePermissions\Observer\AdminhtmlBlockHtmlBeforeObserver',
            ['_removeColumnFromGrid'],
            $constructArguments
        );
        $this->_block = $this->getMock(
            'Magento\Backend\Block\Widget\Grid',
            [
                'getNameInLayout',
                'getMassactionBlock',
                'setCanReadPrice',
                'setCanEditPrice',
                'setTabData',
                'getChildBlock',
                'getParentBlock',
                'setDefaultProductPrice',
                'getForm',
                'getGroup',
            ],
            [],
            '',
            false
        );
        $this->_varienObserver = $this->getMock('Magento\Framework\Event\Observer', ['getBlock', 'getEvent']);
        $this->_varienObserver->expects($this->any())->method('getBlock')->willReturn($this->_block);
    }

    /**
     * @param $blockName string
     * @dataProvider productGridMassactionDataProvider
     */
    public function testAdminhtmlBlockHtmlBeforeProductGridMassaction($blockName)
    {
        $massaction = $this->getMock(
            'Magento\Backend\Block\Widget\Grid\Massaction',
            ['removeItem'],
            [],
            '',
            false
        );
        $massaction->expects($this->once())->method('removeItem')->with($this->equalTo('status'));

        $this->_setGetNameInLayoutExpects($blockName);
        $this->_block->expects($this->once())->method('getMassactionBlock')->willReturn($massaction);
        $this->_assertPriceColumnRemove();

        $this->_observer->execute($this->_varienObserver);
    }

    /**
     * @param $blockName string
     * @dataProvider gridCategoryProductGridDataProvider
     */
    public function testAdminhtmlBlockHtmlBeforeGridCategoryProductGrid($blockName)
    {
        $this->_setGetNameInLayoutExpects($blockName);

        $this->_assertPriceColumnRemove();
        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeCustomerViewCart()
    {
        $this->_setGetNameInLayoutExpects('admin.customer.view.cart');

        $this->_observer->expects(
            $this->exactly(2)
        )->method(
            '_removeColumnFromGrid'
        )->with(
            $this->isInstanceOf('Magento\Backend\Block\Widget\Grid'),
            $this->logicalOr($this->equalTo('price'), $this->equalTo('total'))
        );
        $this->_observer->execute($this->_varienObserver);
    }

    /**
     * @param $blockName string
     * @dataProvider checkoutAccordionDataProvider
     */
    public function testAdminhtmlBlockHtmlBeforeCheckoutAccordion($blockName)
    {
        $this->_setGetNameInLayoutExpects($blockName);

        $this->_assertPriceColumnRemove();
        $this->_observer->execute($this->_varienObserver);
    }

    /**
     * @param $blockName string
     * @dataProvider checkoutItemsDataProvider
     */
    public function testAdminhtmlBlockHtmlBeforeItems($blockName)
    {
        $this->_setGetNameInLayoutExpects($blockName);
        $this->_block->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));
        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeDownloadableLinks()
    {
        $this->_setGetNameInLayoutExpects('catalog.product.edit.tab.downloadable.links');
        $this->_block->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeSuperConfigGrid()
    {
        $this->_setGetNameInLayoutExpects('admin.product.edit.tab.super.config.grid');

        $this->_assertPriceColumnRemove();
        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeProductOptions()
    {
        $this->_setGetNameInLayoutExpects('admin.product.options');

        $childBlock = $this->getMock(
            'Magento\Backend\Block\Template',
            ['setCanEditPrice', 'setCanReadPrice'],
            [],
            '',
            false
        );
        $childBlock->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $childBlock->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));

        $this->_block->expects(
            $this->once()
        )->method(
            'getChildBlock'
        )->with(
            $this->equalTo('options_box')
        )->will(
            $this->returnValue($childBlock)
        );

        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeBundlePrice()
    {
        $this->_setGetNameInLayoutExpects('adminhtml.catalog.product.bundle.edit.tab.attributes.price');
        $this->_block->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setDefaultProductPrice')->with($this->equalTo('default'));
        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeBundleOpt()
    {
        $childBlock = $this->getMock(
            'Magento\Backend\Block\Template',
            ['setCanEditPrice', 'setCanReadPrice'],
            [],
            '',
            false
        );
        $this->_setGetNameInLayoutExpects('adminhtml.catalog.product.edit.tab.bundle.option');
        $childBlock->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));
        $childBlock->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('getChildBlock')->willReturn($childBlock);
        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeCustomerCart()
    {
        $parentBlock = $this->getMock('Magento\Backend\Block\Template', ['getNameInLayout'], [], '', false);
        $parentBlock->expects(
            $this->once()
        )->method(
            'getNameInLayout'
        )->will(
            $this->returnValue('admin.customer.carts')
        );

        $this->_setGetNameInLayoutExpects('customer_cart_');
        $this->_block->expects($this->once())->method('getParentBlock')->willReturn($parentBlock);

        $this->_observer->expects(
            $this->exactly(2)
        )->method(
            '_removeColumnFromGrid'
        )->with(
            $this->isInstanceOf('Magento\Backend\Block\Widget\Grid'),
            $this->logicalOr($this->equalTo('price'), $this->equalTo('total'))
        );

        $this->_observer->execute($this->_varienObserver);
    }

    protected function _assertPriceColumnRemove()
    {
        $this->_observer->expects(
            $this->once()
        )->method(
            '_removeColumnFromGrid'
        )->with(
            $this->isInstanceOf('Magento\Backend\Block\Widget\Grid'),
            $this->equalTo('price')
        );
    }

    protected function _setGetNameInLayoutExpects($blockName)
    {
        $this->_block->expects($this->exactly(2))->method('getNameInLayout')->willReturn($blockName);
    }

    /**
     * @return array
     */
    public function productGridMassactionDataProvider()
    {
        return [['product.grid'], ['admin.product.grid']];
    }

    /**
     * @return array
     */
    public function gridCategoryProductGridDataProvider()
    {
        return [
            ['category.product.grid']
        ];
    }

    /*
     * @return array
     */
    public function checkoutAccordionDataProvider()
    {
        return [
            ['products'],
            ['wishlist'],
            ['compared'],
            ['rcompared'],
            ['rviewed'],
            ['ordered'],
            ['checkout.accordion.products'],
            ['checkout.accordion.wishlist'],
            ['checkout.accordion.compared'],
            ['checkout.accordion.rcompared'],
            ['checkout.accordion.rviewed'],
            ['checkout.accordion.ordered']
        ];
    }

    /**
     * @return array
     */
    public function checkoutItemsDataProvider()
    {
        return [['checkout.items'], ['items']];
    }
}
