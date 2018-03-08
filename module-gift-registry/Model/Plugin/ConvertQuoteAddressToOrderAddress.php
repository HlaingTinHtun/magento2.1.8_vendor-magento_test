<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Model\Plugin;

class ConvertQuoteAddressToOrderAddress
{
    /**
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Address $quoteAddress
     * @param array $data
     * @return \Magento\Sales\Api\Data\OrderAddressInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Address\ToOrderAddress $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Address $quoteAddress,
        $data = []
    ) {
        $orderAddress = $proceed($quoteAddress, $data);
        if ($quoteAddress->getGiftregistryItemId()) {
            $orderAddress->setGiftregistryItemId($quoteAddress->getGiftregistryItemId());
        }
        return $orderAddress;
    }
}
