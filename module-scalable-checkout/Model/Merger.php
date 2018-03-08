<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ScalableCheckout\Model;

class Merger implements \Magento\Framework\MessageQueue\MergerInterface
{
    /**
     * {@inheritdoc}
     */
    public function merge(array $messageList)
    {
        return $messageList;
    }
}
