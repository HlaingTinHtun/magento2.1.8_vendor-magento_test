<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Enterprise\Model\Plugin;

/**
 * Store switcher block plugin
 */
class StoreSwitcher
{
    /**
     * URL for store switcher hint
     */
    const HINT_URL = 'http://docs.magento.com/m2/ee/user_guide/configuration/scope.html';

    /**
     * Return url for store switcher hint
     * @param \Magento\Backend\Block\Store\Switcher $subject
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetHintUrl(\Magento\Backend\Block\Store\Switcher $subject)
    {
        return self::HINT_URL;
    }
}
