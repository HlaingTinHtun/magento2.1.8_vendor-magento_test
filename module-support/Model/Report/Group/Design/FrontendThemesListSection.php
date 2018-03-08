<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Model\Report\Group\Design;

/**
 * Frontend Themes List report
 */
class FrontendThemesListSection extends AbstractDesignSection
{
    /**
     * Generate Themes list information
     *
     * @return array
     */
    public function generate()
    {
        return $this->generateReport(__('Frontend Themes List'));
    }
}