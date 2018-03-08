<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCardAccount\Api;

/**
 * Interface GuestGiftCardAccountManagementInterface
 * @api
 */
interface GuestGiftCardAccountManagementInterface
{
    /**
     * @param string $cartId
     * @param \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface $giftCardAccountData
     * @return bool
     */
    public function addGiftCard(
        $cartId,
        \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface $giftCardAccountData
    );

    /**
     * @param string $cartId
     * @param string $giftCardCode
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return float
     */
    public function checkGiftCard($cartId, $giftCardCode);

    /**
     * Remove GiftCard Account entity
     *
     * @param string $cartId
     * @param string $giftCardCode
     * @return bool
     */
    public function deleteByQuoteId($cartId, $giftCardCode);
}
