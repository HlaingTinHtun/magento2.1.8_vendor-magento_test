<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ScalableInventory\Api\Counter;

/**
 * Interface ItemsInterface
 * @api
 */
interface ItemsInterface
{
    /**
     * @param \Magento\ScalableInventory\Api\Counter\ItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * @return \Magento\ScalableInventory\Api\Counter\ItemInterface[]
     */
    public function getItems();

    /**
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId);

    /**
     * @return int
     */
    public function getWebsiteId();

    /**
     * @param string $operator
     * @return $this
     */
    public function setOperator($operator);

    /**
     * @return string
     */
    public function getOperator();
}
