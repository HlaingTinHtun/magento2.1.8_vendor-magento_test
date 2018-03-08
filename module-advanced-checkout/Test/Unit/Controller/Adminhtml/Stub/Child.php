<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCheckout\Test\Unit\Controller\Adminhtml\Stub;

class Child extends \Magento\AdvancedCheckout\Controller\Adminhtml\Index
{
    public function execute()
    {
        $this->_initData();
    }
}
