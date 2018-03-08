<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardAccount\Controller\Cart;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * No index action, forward to 404
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('noroute');
    }
}
