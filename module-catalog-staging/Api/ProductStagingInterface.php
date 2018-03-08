<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogStaging\Api;

/**
 * Interface ProductStagingInterface
 * @api
 */
interface ProductStagingInterface
{
    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $version
     * @param array $arguments
     * @return bool
     */
    public function schedule(\Magento\Catalog\Api\Data\ProductInterface $product, $version, $arguments = []);

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $version
     * @return bool
     */
    public function unschedule(\Magento\Catalog\Api\Data\ProductInterface $product, $version);
}
