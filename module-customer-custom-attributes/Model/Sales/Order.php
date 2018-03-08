<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Model\Sales;

/**
 * Customer Order model
 *
 * @method \Magento\CustomerCustomAttributes\Model\ResourceModel\Sales\Order _getResource()
 * @method \Magento\CustomerCustomAttributes\Model\ResourceModel\Sales\Order getResource()
 * @method \Magento\CustomerCustomAttributes\Model\Sales\Order setEntityId(int $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Order extends AbstractSales
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\CustomerCustomAttributes\Model\ResourceModel\Sales\Order');
    }
}
