<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardAccount\Block\Adminhtml;

class Giftcardaccount extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_giftcardaccount';
        $this->_blockGroup = 'Magento_GiftCardAccount';
        $this->_headerText = __('Gift Card Accounts');
        $this->_addButtonLabel = __('Add Gift Card Account');
        parent::_construct();
    }
}
