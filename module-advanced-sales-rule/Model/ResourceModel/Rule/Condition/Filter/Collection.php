<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdvancedSalesRule\Model\ResourceModel\Rule\Condition\Filter;

/**
 * Class Collection
 * @package Magento\AdvancedSalesRule\Model\ResourceModel\Rule\Condition\Filter
 * @codeCoverageIgnore
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Set resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\AdvancedSalesRule\Model\Rule\Condition\Filter',
            'Magento\AdvancedSalesRule\Model\ResourceModel\Rule\Condition\Filter'
        );
    }
}
