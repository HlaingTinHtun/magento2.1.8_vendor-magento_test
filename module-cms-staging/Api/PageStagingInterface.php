<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CmsStaging\Api;

/**
 * Interface PageStagingInterface
 * @api
 */

interface PageStagingInterface
{
    /**
     * @param \Magento\Cms\Api\Data\PageInterface $page
     * @param string $version
     * @param array $arguments
     * @return bool
     */
    public function schedule(\Magento\Cms\Api\Data\PageInterface $page, $version, $arguments = []);

    /**
     * @param \Magento\Cms\Api\Data\PageInterface $page
     * @param string $version
     * @return bool
     */
    public function unschedule(\Magento\Cms\Api\Data\PageInterface $page, $version);
}
