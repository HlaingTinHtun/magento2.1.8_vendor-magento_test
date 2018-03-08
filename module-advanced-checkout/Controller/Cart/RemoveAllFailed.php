<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCheckout\Controller\Cart;

class RemoveAllFailed extends \Magento\AdvancedCheckout\Controller\Cart
{
    /**
     * Remove all failed items from storage
     *
     * @return void
     */
    public function execute()
    {
        $this->_getFailedItemsCart()->removeAllAffectedItems();
        $this->messageManager->addSuccess(__('You removed the items.'));
        $this->_redirect('checkout/cart');
    }
}
