<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Api\Data;

/**
 * Interface RmaSearchResultInterface
 * @api
 */
interface RmaSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Rma list
     *
     * @return \Magento\Rma\Api\Data\RmaInterface[]
     */
    public function getItems();

    /**
     * Set Rma list
     *
     * @param \Magento\Rma\Api\Data\RmaInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
