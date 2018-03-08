<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Controller\Search;

/**
 * @codeCoverageIgnore
 */
class Index extends \Magento\GiftRegistry\Controller\Search
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Gift Registry Search'));
        $this->_view->renderLayout();
    }
}
