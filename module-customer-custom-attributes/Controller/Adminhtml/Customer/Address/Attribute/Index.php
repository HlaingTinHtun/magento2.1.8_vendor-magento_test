<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Address\Attribute;

class Index extends \Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Address\Attribute
{
    /**
     * Attributes grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Customer Address Attributes'));
        $this->_view->renderLayout();
    }
}
