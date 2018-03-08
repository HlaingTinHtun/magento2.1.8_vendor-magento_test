<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Related banners edit tab for promo sales rule edit page
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Banner\Block\Adminhtml\Promo\Salesrule\Edit\Tab;

class Banners extends \Magento\Backend\Block\Text\ListText implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Related Banners');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Related Banners');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return null
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }
}
