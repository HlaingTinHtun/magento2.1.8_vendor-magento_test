<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Model\Report\Group\Events;

use Magento\Framework\App\Area;
use Magento\Framework\Event\ConfigInterface;

/**
 * Custom frontend events section
 */
class CustomFrontendEventsSection extends AbstractEventsSection
{
    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return (string)__('Custom Frontend Events');
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return ConfigInterface::TYPE_CUSTOM;
    }

    /**
     * {@inheritdoc}
     */
    public function getAreaCode()
    {
        return Area::AREA_FRONTEND;
    }
}