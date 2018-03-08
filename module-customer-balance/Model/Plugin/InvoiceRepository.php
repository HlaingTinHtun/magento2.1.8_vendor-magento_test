<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerBalance\Model\Plugin;

class InvoiceRepository
{
    /**
     * Add customer balance amount information to invoice
     *
     * @param \Magento\Sales\Model\Order\InvoiceRepository $subject
     * @param \Magento\Sales\Api\Data\InvoiceInterface $entity
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        \Magento\Sales\Model\Order\InvoiceRepository $subject,
        \Magento\Sales\Api\Data\InvoiceInterface $entity
    ) {
        $extensionAttributes = $entity->getExtensionAttributes();
        if ($extensionAttributes) {
            $entity->setCustomerBalanceAmount($extensionAttributes->getCustomerBalanceAmount());
            $entity->setBaseCustomerBalanceAmount($extensionAttributes->getBaseCustomerBalanceAmount());
        }
    }
}
