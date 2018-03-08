<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VisualMerchandiser\Model\ResourceModel\Rules;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource table
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magento\VisualMerchandiser\Model\Rules', 'Magento\VisualMerchandiser\Model\ResourceModel\Rules');
    }
}
