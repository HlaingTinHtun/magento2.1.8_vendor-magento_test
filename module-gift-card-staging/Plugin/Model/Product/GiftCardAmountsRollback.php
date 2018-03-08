<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardStaging\Plugin\Model\Product;

use Magento\GiftCard\Model\Catalog\Product\Type\Giftcard;
use Magento\GiftCard\Model\Product\SaveHandler;
use Magento\Staging\Model\VersionManager;

/**
 * Class GiftCardAmountsRollback is a plugin which implements rollback for
 * GiftCard amounts extension attribute in case saving staging version
 */
class GiftCardAmountsRollback
{
    /**
     * @var VersionManager
     */
    private $versionManager;

    /**
     * ExtensionAttributesRollback constructor.
     *
     * @param VersionManager $versionManager
     */
    public function __construct(
        VersionManager $versionManager
    ) {
        $this->versionManager = $versionManager;
    }

    /**
     * Plugin main method
     *
     * @param SaveHandler $subject
     * @param \Magento\Catalog\Api\Data\ProductInterface $entity
     * @param array $arguments
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(SaveHandler $subject, $entity, $arguments = [])
    {
        if ($entity->getTypeId() != Giftcard::TYPE_GIFTCARD) {
            return;
        }

        $giftCardAmounts = $entity->getExtensionAttributes()
            ->getGiftcardAmounts();

        if (
            $giftCardAmounts != null &&
            $this->versionManager->getCurrentVersion()->getIsRollback()
        ) {
            foreach ($giftCardAmounts as $giftCardAmount) {
                $giftCardAmount->setValue(
                    $this->floatalize($giftCardAmount->getWebsiteValue())
                );
            }
        }
    }

    /**
     * Formats a string as a currency string
     *
     * @param string $value
     * @return string
     */
    private function floatalize($value)
    {
        return !empty($value) ? number_format($value, 2, '.', '') : '';
    }
}
