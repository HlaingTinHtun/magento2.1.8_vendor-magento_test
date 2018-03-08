<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category;

class PermissionsContainer extends \Magento\Backend\Block\Template
{
    /**
     * @return string
     */
    public function toHtml()
    {
        return $this->getLayout()->createBlock(
            'Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions',
            'category.permissions.row'
        )->toHtml();
    }
}
