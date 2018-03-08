<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdminGws\Observer;

use Magento\Framework\Event\ObserverInterface;

class DisallowCreateAttributeButtonDisplay implements ObserverInterface
{
    /**
     * @var \Magento\AdminGws\Model\Blocks
     */
    protected $blocks;

    /**
     * @param \Magento\AdminGws\Model\Blocks $blocks
     */
    public function __construct(
        \Magento\AdminGws\Model\Blocks $blocks
    ) {
        $this->blocks = $blocks;
    }

    /**
     * Update role store group ids in helper and role
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->blocks->disallowCreateAttributeButtonDisplay($observer);
    }
}
