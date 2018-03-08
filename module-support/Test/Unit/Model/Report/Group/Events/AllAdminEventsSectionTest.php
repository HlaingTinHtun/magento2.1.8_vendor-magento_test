<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Group\Events;

use Magento\Framework\App\Area;

class AllAdminEventsSectionTest extends AbstractEventsSectionTest
{
    /**
     * {@inheritdoc}
     */
    protected function getSectionName()
    {
        return 'Magento\Support\Model\Report\Group\Events\AllAdminEventsSection';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedTitle()
    {
        return (string)__('All Admin Events');
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedType()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedAreaCode()
    {
        return Area::AREA_ADMINHTML;
    }
}