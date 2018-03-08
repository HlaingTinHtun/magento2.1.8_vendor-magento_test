<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PricePermissions\Ui\DataProvider\Product\Form\Modifier\Plugin;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Eav as EavModifier;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\PricePermissions\Observer\ObserverData;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\GiftCard\Model\Giftcard\Amount as GiftCardAmount;
use Magento\GiftCard\Model\Catalog\Product\Type\Giftcard as GiftCardType;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

/**
 * Class EavModifier
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Eav
{
    const META_ATTRIBUTE_CONFIG_PATH = 'arguments/data/config';

    /**
     * @var ObserverData
     */
    private $observerData;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @param ObserverData $observerData
     * @param ArrayManager $arrayManager
     * @param LocatorInterface $locator
     */
    public function __construct(ObserverData $observerData, ArrayManager $arrayManager, LocatorInterface $locator)
    {
        $this->observerData = $observerData;
        $this->arrayManager = $arrayManager;
        $this->locator = $locator;
    }

    /**
     * Around setup attribute meta
     *
     * @param EavModifier $subject
     * @param \Closure $proceed
     * @param ProductAttributeInterface $attribute
     * @param string $groupCode
     * @param int $sortOrder
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSetupAttributeMeta(
        EavModifier $subject,
        \Closure $proceed,
        ProductAttributeInterface $attribute,
        $groupCode,
        $sortOrder
    ) {
        if (
            $attribute->getAttributeCode() === ProductAttributeInterface::CODE_STATUS
            && !$this->observerData->isCanEditProductStatus()
        ) {
            return $this->addDisabledMetaConfig($proceed($attribute, $groupCode, $sortOrder));
        }

        $attributeMeta = $proceed($attribute, $groupCode, $sortOrder);

        return $this->restrictPriceAccess($attributeMeta, $attribute);
    }

    /**
     * Around setup attribute container meta
     *
     * @param EavModifier $subject
     * @param \Closure $proceed
     * @param ProductAttributeInterface $attribute
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSetupAttributeContainerMeta(
        EavModifier $subject,
        \Closure $proceed,
        ProductAttributeInterface $attribute
    ) {
        $attributeMeta = $proceed($attribute);

        return $this->restrictPriceAccess($attributeMeta, $attribute);
    }

    /**
     * Restrict price access
     *
     * @param array $attributeMeta
     * @param ProductAttributeInterface $attribute
     * @return array
     */
    private function restrictPriceAccess(array $attributeMeta, ProductAttributeInterface $attribute)
    {
        if (!$this->isPrice($attribute)) {
            return $attributeMeta;
        }

        if (!$this->observerData->isCanReadProductPrice()) {
            $attributeMeta = $this->addHiddenMetaConfig($attributeMeta);
        } elseif (!$this->observerData->isCanEditProductPrice()) {
            $attributeMeta = $this->addDisabledMetaConfig($attributeMeta);
        }

        return $attributeMeta;
    }

    /**
     * Check is price attribute
     *
     * @param ProductAttributeInterface $attribute
     * @return bool
     */
    private function isPrice(ProductAttributeInterface $attribute)
    {
        $priceCodes = [ProductAttributeInterface::CODE_TIER_PRICE, 'price_type', 'allow_open_amount'];

        return
            $attribute->getFrontendInput() === 'price'
            || in_array($attribute->getAttributeCode(), $priceCodes);
    }

    /**
     * Disable attribute
     *
     * @param array $attributeMeta
     * @return array
     */
    private function addDisabledMetaConfig(array $attributeMeta)
    {
        return $this->arrayManager->merge(static::META_ATTRIBUTE_CONFIG_PATH, $attributeMeta, [
            'disabled' => true,
            'validation' => [
                'required' => false
            ],
            'required' => false,
        ]);
    }

    /**
     * Hide attribute
     *
     * @param array $attributeMeta
     * @return array
     */
    private function addHiddenMetaConfig(array $attributeMeta)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();

        if ($product->isObjectNew()) {
            return $this->arrayManager->merge(static::META_ATTRIBUTE_CONFIG_PATH, $attributeMeta, [
                'visible' => false,
                'validation' => [
                    'required' => false
                ],
                'required' => false
            ]);
        }

        return [];
    }

    /**
     * After setup attribute data
     *
     * @param EavModifier $subject
     * @param \Closure $proceed
     * @param ProductAttributeInterface $attribute
     * @return float|int|mixed|string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSetupAttributeData(
        EavModifier $subject,
        \Closure $proceed,
        ProductAttributeInterface $attribute
    ) {
        /** @var mixed $attributeData */
        $attributeData = $proceed($attribute);

        try {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->locator->getProduct();
        } catch (NoSuchEntityException $e) {
            return $attributeData;
        }

        if (
            $attribute->getAttributeCode() === ProductAttributeInterface::CODE_STATUS
            && !$this->observerData->isCanEditProductStatus()
            && $product->isObjectNew()
        ) {
            return Status::STATUS_DISABLED;
        }

        if (!$this->isPrice($attribute)) {
            return $attributeData;
        }

        if (
            !$this->observerData->isCanEditProductPrice() &&
            $product->isObjectNew() &&
            $product->getTypeId() !== ProductType::TYPE_BUNDLE
        ) {
            return $this->getDefaultPriceValue($attribute, $attributeData);
        }

        return $attributeData;
    }

    /**
     * Get default price value
     *
     * @param ProductAttributeInterface $attribute
     * @param mixed $attributeData
     * @return float|int|string
     */
    private function getDefaultPriceValue(ProductAttributeInterface $attribute, $attributeData)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        $defaultProductPriceString = $this->observerData->getDefaultProductPriceString();

        if ($product->getTypeId() !== GiftCardType::TYPE_GIFTCARD) {
            return $attributeData;
        }

        switch ($attribute->getAttributeCode()) {
            case GiftCardAmount::KEY_WEBSITE_ID:
                return $this->locator->getStore()->getWebsiteId();
            case GiftCardAmount::KEY_VALUE:
            case GiftCardAmount::KEY_WEBSITE_VALUE:
                return (double)$defaultProductPriceString;
        }

        return $attributeData;
    }
}
