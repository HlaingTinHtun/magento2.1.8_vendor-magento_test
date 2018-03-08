<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogEvent\Model;

class DateResolver
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * Core store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->localeDate = $localeDate;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve configuration timezone
     *
     * @return string
     */
    public function getConfigTimezone()
    {
        return $this->localeDate->getConfigTimezone();
    }

    /**
     * Retrieve store timezone from configuration
     *
     * @return string
     */
    public function getConfigStoreTimezone()
    {
        return $this->localeDate->getConfigTimezone(
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore($this->storeManager->getStore()->getId())->getCode()
        );
    }

    /**
     * Convert date to UTC according to store timezone if $toUtc is true
     * or to store timezone if $toUtc is false
     *
     * @param string $date
     * @param bool $toUtc
     * @param null|string $format
     * @return string
     */
    public function convertDate($date, $toUtc = true, $format = null)
    {
        if ($toUtc) {
            $dateObj = new \DateTime($date, new \DateTimeZone($this->getConfigTimezone()));
            $dateObj->setTimezone(new \DateTimeZone('UTC'));
        } else {
            $dateObj = new \DateTime($date, new \DateTimeZone('UTC'));
            $dateObj->setTimezone(new \DateTimeZone($this->getConfigTimezone()));
        }

        if ($format) {
            return  $dateObj->format($format);
        }

        return  $dateObj->format('Y-m-d H:i:s');
    }
}
