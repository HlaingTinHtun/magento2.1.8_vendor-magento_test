<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer Order By SKU block
 * @codeCoverageIgnore
 */
namespace Magento\AdvancedCheckout\Block\Customer;

class Sku extends \Magento\AdvancedCheckout\Block\Sku\AbstractSku
{
    /**
     * Retrieve form action URL
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('customer_order/sku/uploadFile');
    }

    /**
     * Check whether form should be multipart
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsMultipart()
    {
        return true;
    }
}
