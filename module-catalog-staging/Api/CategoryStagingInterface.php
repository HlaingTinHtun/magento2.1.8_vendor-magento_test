<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogStaging\Api;

/**
 * Class CategoryStagingInterface
 * @api
 */
interface CategoryStagingInterface
{
    /**
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @param string $version
     * @param array $arguments
     * @return bool
     */
    public function schedule(\Magento\Catalog\Api\Data\CategoryInterface $category, $version, $arguments = []);

    /**
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @param string $version
     * @return bool
     */
    public function unschedule(\Magento\Catalog\Api\Data\CategoryInterface $category, $version);
}
