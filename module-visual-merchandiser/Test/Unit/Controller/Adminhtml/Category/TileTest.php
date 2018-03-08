<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Test\Unit\Controller\Adminhtml\Category;

class TileTest extends AbstractGrid
{
    /**
     * Defines which controller is to be used
     * @var string
     */
    protected $controllerClass = 'Magento\VisualMerchandiser\Controller\Adminhtml\Category\Tile';

    /**
     * Set up expected parameters and call super
     * @return void
     */
    public function testExecute()
    {
        $expectedBlock = 'Magento\VisualMerchandiser\Block\Adminhtml\Category\Merchandiser\Tile';
        $expectedId = 'tile';
        $this->progressTest($expectedBlock, $expectedId);
    }
}
