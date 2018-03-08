<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Model\ResourceModel\Segment\Report;

class Collection extends \Magento\CustomerSegment\Model\ResourceModel\Segment\Collection
{
    /**
     * @return \Magento\CustomerSegment\Model\ResourceModel\Segment\Report\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCustomerCountToSelect();
        return $this;
    }
}
