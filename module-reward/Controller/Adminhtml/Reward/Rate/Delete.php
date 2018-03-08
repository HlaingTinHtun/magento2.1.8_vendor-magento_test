<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Controller\Adminhtml\Reward\Rate;

class Delete extends \Magento\Reward\Controller\Adminhtml\Reward\Rate
{
    /**
     * Delete Action
     *
     * @return void
     */
    public function execute()
    {
        $rate = $this->_initRate();
        if ($rate->getId()) {
            try {
                $rate->delete();
                $this->messageManager->addSuccess(__('You deleted the rate.'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', ['_current' => true]);
                return;
            }
        }

        return $this->_redirect('adminhtml/*/');
    }
}
