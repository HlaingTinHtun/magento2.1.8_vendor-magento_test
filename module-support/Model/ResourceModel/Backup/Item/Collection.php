<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Model\ResourceModel\Backup\Item;

/**
 * Collection of backup items
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Init Resource Model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Support\Model\Backup\AbstractItem', 'Magento\Support\Model\ResourceModel\Backup\Item');
    }
}
