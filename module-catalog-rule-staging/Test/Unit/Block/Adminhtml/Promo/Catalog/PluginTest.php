<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogRuleStaging\Test\Unit\Block\Adminhtml\Promo\Catalog;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class PluginTest
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{
    public function testBeforeCanRender()
    {
        $applyButtonId = 'apply_rules';
        $blockMock = $this->getMockBuilder(\Magento\CatalogRule\Block\Adminhtml\Promo\Catalog::class)
            ->disableOriginalConstructor()
            ->getMock();
        $buttonMock = $this->getMockBuilder(\Magento\Backend\Block\Widget\Button\Item::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $helper = new ObjectManager($this);
        /**
         * @var $plugin \Magento\CatalogRuleStaging\Block\Adminhtml\Promo\Catalog\Plugin
         */
        $plugin = $helper->getObject(\Magento\CatalogRuleStaging\Block\Adminhtml\Promo\Catalog\Plugin::class);
        $buttonMock->expects($this->exactly(3))->method('getId')
            ->willReturnOnConsecutiveCalls($applyButtonId, $applyButtonId, 'another_button');
        $blockMock->expects($this->once())->method('removeButton')->with($applyButtonId);
        $this->assertSame([], $plugin->beforeCanRender($blockMock, $buttonMock));
        $this->assertSame([$buttonMock], $plugin->beforeCanRender($blockMock, $buttonMock));
    }
}
