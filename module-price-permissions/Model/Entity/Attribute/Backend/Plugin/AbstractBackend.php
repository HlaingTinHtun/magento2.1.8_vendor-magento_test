<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PricePermissions\Model\Entity\Attribute\Backend\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\PricePermissions\Observer\ObserverData;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend as EavAbstractBackend;

/**
 * Class Price
 */
class AbstractBackend
{
    /**
     * @var ObserverData
     */
    private $observerData;

    /**
     * @param ObserverData $observerData
     */
    public function __construct(ObserverData $observerData)
    {
        $this->observerData = $observerData;
    }

    /**
     * Around validate
     *
     * @param EavAbstractBackend $subject
     * @param \Closure $proceed
     * @param mixed $object
     * @return mixed
     */
    public function aroundValidate(
        EavAbstractBackend $subject,
        \Closure $proceed,
        $object
    ) {
        if (!$object instanceof ProductInterface) {
            return $proceed($object);
        }

        if (
            !$this->observerData->isCanReadProductPrice()
            && $object->isObjectNew()
            && $this->isPrice($subject->getAttribute())
        ) {
            $object->setData(
                $subject->getAttribute()->getAttributeCode(),
                $this->observerData->getDefaultProductPriceString()
            );
        }

        return $proceed($object);
    }

    /**
     * Check is price attribute
     *
     * @param AbstractAttribute $attribute
     * @return bool
     */
    private function isPrice(AbstractAttribute $attribute)
    {
        return $attribute->getFrontendInput() === 'price';
    }
}
