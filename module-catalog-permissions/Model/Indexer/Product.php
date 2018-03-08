<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Model\Indexer;

class Product extends Category
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'catalogpermissions_product';

    /**
     * @param Category\Action\FullFactory $fullActionFactory
     * @param Product\Action\RowsFactory $rowsActionFactory
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     */
    public function __construct(
        Category\Action\FullFactory $fullActionFactory,
        Product\Action\RowsFactory $rowsActionFactory,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        parent::__construct($fullActionFactory, $rowsActionFactory, $indexerRegistry);
    }

    /**
     * Add tags to cache context
     *
     * @return void
     */
    protected function registerTags()
    {
        $this->getCacheContext()->registerTags(
            [
                \Magento\Catalog\Model\Category::CACHE_TAG,
                \Magento\Catalog\Model\Product::CACHE_TAG
            ]
        );
    }

    /**
     * Add entities to cache context
     *
     * @param int[] $ids
     * @return void
     */
    protected function registerEntities($ids)
    {
        $this->getCacheContext()->registerEntities(\Magento\Catalog\Model\Product::CACHE_TAG, $ids);
    }
}
