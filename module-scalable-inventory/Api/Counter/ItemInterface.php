<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ScalableInventory\Api\Counter;

/**
 * Interface ItemInterface
 * @api
 */
interface ItemInterface
{
    /**
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * @return int
     */
    public function getQty();
}
