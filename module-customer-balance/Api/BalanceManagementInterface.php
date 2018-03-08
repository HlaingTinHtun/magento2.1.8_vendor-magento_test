<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerBalance\Api;

/**
 * Customer balance(store credit) operations
 * @api
 */
interface BalanceManagementInterface
{
    /**
     * Apply store credit
     *
     * @param int $cartId
     * @return bool
     */
    public function apply($cartId);
}
