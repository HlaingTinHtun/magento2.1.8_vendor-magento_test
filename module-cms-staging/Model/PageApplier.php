<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CmsStaging\Model;

use Magento\Framework\Indexer\CacheContext;
use Magento\Staging\Model\StagingApplierInterface;

/**
 * Class PageApplier
 */
class PageApplier implements StagingApplierInterface
{
    /** @var CacheContext */
    private $cacheContext;

    /**
     * PageStagingApplier constructor
     * @param CacheContext $cacheContext
     */
    public function __construct(
        CacheContext $cacheContext
    ) {
        $this->cacheContext = $cacheContext;
    }

    /**
     * @param array $entityIds
     */
    public function execute(array $entityIds)
    {
        if ($entityIds) {
            $this->cacheContext->registerEntities(\Magento\Cms\Model\Page::CACHE_TAG, $entityIds);
        }
    }
}
