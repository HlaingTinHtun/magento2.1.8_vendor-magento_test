<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedRule\Model\Condition;

interface FilterGroupInterface
{
    /**
     * @return FilterInterface[]
     */
    public function getFilters();

    /**
     * @param FilterInterface[] $filters
     * @return $this
     */
    public function setFilters($filters);
}
