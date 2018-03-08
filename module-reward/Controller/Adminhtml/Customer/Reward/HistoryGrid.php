<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Controller\Adminhtml\Customer\Reward;

/**
 * @codeCoverageIgnore
 */
class HistoryGrid extends \Magento\Reward\Controller\Adminhtml\Customer\Reward
{
    /**
     * History Grid Ajax Action
     *
     * @return void
     *
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
