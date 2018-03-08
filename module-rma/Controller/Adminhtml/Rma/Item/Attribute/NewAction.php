<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute;

class NewAction extends \Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute
{
    /**
     * Create new attribute action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->addActionLayoutHandles();
        $this->_forward('edit');
    }
}
