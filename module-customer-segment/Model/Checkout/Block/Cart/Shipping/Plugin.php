<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerSegment\Model\Checkout\Block\Cart\Shipping;

class Plugin
{
    /**
     * @var \Magento\Quote\Api\Data\EstimateAddressInterfaceFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param \Magento\Quote\Api\Data\EstimateAddressInterfaceFactory $addressFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Quote\Api\Data\EstimateAddressInterfaceFactory $addressFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->addressFactory = $addressFactory;
        $this->quoteRepository = $quoteRepository;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param \Magento\Checkout\Model\Cart\CollectQuote $subject
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Closure $proceed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return mixed
     */
    public function aroundCollect(
        \Magento\Checkout\Model\Cart\CollectQuote $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote $quote
    ) {
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerRepository->getById($this->customerSession->getCustomerId());
            if (!$customer->getDefaultShipping()) {
                $quote->setTotalsCollectedFlag(false);
                $quote->collectTotals();
                $this->quoteRepository->save($quote);
            }
        }
        return $proceed($quote);
    }
}
