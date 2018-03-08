<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy;

class PageGrid extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy
{
    /**
     * Cms Pages Ajax Grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
