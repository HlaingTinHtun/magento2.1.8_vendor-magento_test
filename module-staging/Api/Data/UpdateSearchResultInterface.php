<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Api\Data;

/**
 * Update search result interface.
 * @api
 */
interface UpdateSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Gets collection items
     *
     * @return \Magento\Staging\Api\Data\UpdateInterface[]
     */
    public function getItems();

    /**
     * Set collection items
     *
     * @param \Magento\Staging\Api\Data\UpdateInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
