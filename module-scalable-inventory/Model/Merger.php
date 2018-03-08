<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ScalableInventory\Model;

use Magento\Framework\MessageQueue\EnvelopeInterface;
use Magento\Framework\MessageQueue\MergerInterface;
use Magento\ScalableInventory\Api\Counter\ItemsInterface;
use Magento\ScalableInventory\Model\Counter\ItemsBuilder;

class Merger implements MergerInterface
{
    /**
     * @var ItemsBuilder
     */
    private $itemsBuilder;

    /**
     * @param ItemsBuilder $itemsBuilder
     */
    public function __construct(ItemsBuilder $itemsBuilder)
    {
        $this->itemsBuilder = $itemsBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $messageList)
    {
        $arguments = [];
        foreach ($messageList as $topic => $messages) {
            /** @var ItemsInterface[] $messages */
            foreach ($messages as $message) {
                $items = $message->getItems();
                $operator = $message->getOperator();
                $websiteId = $message->getWebsiteId();
                foreach ($items as $item) {
                    $productId = $item->getProductId();
                    $qty = $item->getQty();
                    if (isset($arguments[$topic][$operator][$websiteId][$item->getProductId()])) {
                        $arguments[$topic][$operator][$websiteId][$productId] += $qty;
                    } else {
                        $arguments[$topic][$operator][$websiteId][$productId] = $qty;
                    }
                }
            }
        }

        $mergedMessages = [];
        foreach ($arguments as $topic => $args) {
            foreach ($args as $operator => $argumentsByOperator) {
                foreach ($argumentsByOperator as $websiteId => $argumentByWebsiteId) {
                    $mergedMessages[$topic][] = $this->itemsBuilder->build($argumentByWebsiteId, $websiteId, $operator);
                }
            }
        }

        return $mergedMessages;
    }
}
