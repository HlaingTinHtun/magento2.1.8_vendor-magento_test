<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Invitation\Block\Adminhtml\Report\Invitation;

/**
 * Backend invitation order report page content block
 *
 */
class Order extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_report_invitation_order';
        $this->_blockGroup = 'Magento_Invitation';
        $this->_headerText = __('Order Conversion Rate');
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
