<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Api\Data;

/**
 * Interface for hierarchy node search results.
 * @api
 */
interface HierarchyNodeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get nodes list.
     *
     * @return \Magento\VersionsCms\Api\Data\HierarchyNodeInterface[]
     */
    public function getItems();

    /**
     * Set nodes list.
     *
     * @param \Magento\VersionsCms\Api\Data\HierarchyNodeInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
