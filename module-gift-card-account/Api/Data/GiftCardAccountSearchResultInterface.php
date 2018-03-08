<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardAccount\Api\Data;

/**
 * Interface GiftCardAccountSearchResultInterface
 * @api
 */
interface GiftCardAccountSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get GiftCard Account list
     *
     * @return \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface[]
     */
    public function getItems();
}
