<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerBalance\Controller\Adminhtml\Customerbalance;

class DeleteOrphanBalances extends \Magento\CustomerBalance\Controller\Adminhtml\Customerbalance
{
    /**
     * Delete orphan balances
     *
     * @return void
     */
    public function execute()
    {
        $this->_balance->deleteBalancesByCustomerId((int)$this->getRequest()->getParam('id'));
        $this->_redirect('customer/index/edit/', ['_current' => true]);
    }
}
