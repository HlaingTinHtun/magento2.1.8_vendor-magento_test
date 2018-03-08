<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedRule\Model\Condition;

interface FilterTextGeneratorInterface
{
    /**
     * @param \Magento\Framework\DataObject $input
     * @return string[]
     */
    public function generateFilterText(\Magento\Framework\DataObject $input);
}
