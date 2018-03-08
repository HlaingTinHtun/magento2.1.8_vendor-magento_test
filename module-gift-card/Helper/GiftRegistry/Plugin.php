<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCard\Helper\GiftRegistry;

use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote\Item;
use Magento\GiftCard\Model\Catalog\Product\Type\Giftcard as ProductType;

class Plugin
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Magento\GiftRegistry\Helper\Data $subject
     * @param callable $proceed
     * @param Item|Product $item
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCanAddToGiftRegistry(
        \Magento\GiftRegistry\Helper\Data $subject,
        \Closure $proceed,
        $item
    ) {
        // skip following calculations for virtual product or cart item
        if (!$proceed($item)) {
            return false;
        }

        if ($item instanceof Item) {
            $productType = $item->getProductType();
        } else {
            $productType = $item->getTypeId();
        }

        if ($productType == ProductType::TYPE_GIFTCARD) {
            if ($item instanceof Item) {
                $product = $this->productRepository->getById($item->getProductId());
            } else {
                $product = $item;
            }
            return $product->getTypeInstance()->isTypePhysical($product);
        }

        return true;
    }
}
