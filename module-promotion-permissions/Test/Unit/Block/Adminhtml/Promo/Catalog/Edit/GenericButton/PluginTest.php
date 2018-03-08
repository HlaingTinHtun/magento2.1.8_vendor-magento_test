<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PromotionPermissions\Test\Unit\Block\Adminhtml\Promo\Catalog\Edit\GenericButton;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PromotionPermissions\Block\Adminhtml\Promo\Catalog\Edit\GenericButton\Plugin
     */
    protected $model;

    /**
     * @param bool $canEdit
     * @param string $name
     * @param bool $expectedResult
     * @dataProvider afterCanRenderDataProvider
     */
    public function testAfterCanRender($canEdit, $name, $expectedResult)
    {
        $permissionsDataMock = $this->getMock('Magento\PromotionPermissions\Helper\Data', [], [], '', false);
        $permissionsDataMock->expects($this->once())->method('getCanAdminEditCatalogRules')->willReturn($canEdit);
        $buttonMock = $this->getMock('Magento\CatalogRule\Block\Adminhtml\Edit\GenericButton', [], [], '', false);

        $model = new \Magento\PromotionPermissions\Block\Adminhtml\Promo\Catalog\Edit\GenericButton\Plugin(
            $permissionsDataMock
        );
        $this->assertEquals($expectedResult, $model->afterCanRender($buttonMock, $name));
    }

    /**
     * @return array
     */
    public function afterCanRenderDataProvider()
    {
        return [
            [true, 'any', true],
            [false, 'delete', false],
            [false, 'turbo', true],
            [false, 'save_and_continue_edit', false]
        ];
    }
}
