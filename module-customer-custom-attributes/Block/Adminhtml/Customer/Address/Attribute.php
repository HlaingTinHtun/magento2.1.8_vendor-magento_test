<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Address;

/**
 * Customer address attributes Grid Container
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Attribute extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Define controller, block and labels
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_CustomerCustomAttributes';
        $this->_controller = 'adminhtml_customer_address_attribute';
        $this->_headerText = __('Customer Address Attributes');
        $this->_addButtonLabel = __('Add New Attribute');
        parent::_construct();
    }
}
