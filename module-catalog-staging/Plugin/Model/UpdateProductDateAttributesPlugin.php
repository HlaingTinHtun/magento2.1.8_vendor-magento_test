<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogStaging\Plugin\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Staging\Model\VersionManager;

/**
 * Class AfterProductRepositorySave
 *
 * Plugin to update datetime attributes related to product entity.
 * Specified attributes disabled on product form for Staging, but should be
 * synchronized with update entity date range.
 */
class UpdateProductDateAttributesPlugin
{
    /**
     * List of start date attributes related to product entity
     * @var array
     */
    private static $startDateKeys = [
        'news_from_date', 'special_from_date', 'custom_design_from'
    ];

    /**
     * List of end date attributes related to product entity
     * @var array
     */
    private static $endDateKeys = [
        'news_to_date', 'special_to_date', 'custom_design_to'
    ];

    /**
     * List of date attributes
     * @var array
     */
    private static $dateKeys = [
        'news_from_date' => 'is_new',
        'news_to_date' => 'is_new'
    ];

    /**
     * @var VersionManager
     */
    private $versionManager;

    /**
     * AfterProductRepositorySave constructor.
     * @param VersionManager $versionManager
     */
    public function __construct(
        VersionManager $versionManager
    ) {
        $this->versionManager = $versionManager;
    }

    /**
     * Triggers before save product entity via repository
     * @param ProductResource $subject
     * @param ProductInterface $object
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        ProductResource $subject,
        $object
    ) {
        $version = $this->versionManager->getCurrentVersion();
        if ($version->getStartTime()) {
            $this->setDateTime($object, self::$startDateKeys, $version->getStartTime());
            $this->setDateTime($object, self::$endDateKeys, $version->getEndTime());
        }
    }

    /**
     * Update product date attributes with received date
     * @param ProductInterface $object
     * @param array $keys
     * @param string $time
     * @return void
     */
    private function setDateTime(ProductInterface $object, array $keys, $time)
    {
        foreach ($keys as $key) {
            $value = $time;
            if (isset(self::$dateKeys[$key]) && !$object->getData(self::$dateKeys[$key])) {
                $value = null;
            }
            $object->setData($key, $value);
        }
    }
}
