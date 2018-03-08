<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CmsStaging\Api;

/**
 * Class BlockStagingInterface
 * @api
 */
interface BlockStagingInterface
{
    /**
     * @param \Magento\Cms\Api\Data\BlockInterface $block
     * @param string $version
     * @param array $arguments
     * @return bool
     */
    public function schedule(\Magento\Cms\Api\Data\BlockInterface $block, $version, $arguments = []);

    /**
     * @param \Magento\Cms\Api\Data\BlockInterface $block
     * @param string $version
     * @return bool
     */
    public function unschedule(\Magento\Cms\Api\Data\BlockInterface $block, $version);
}
