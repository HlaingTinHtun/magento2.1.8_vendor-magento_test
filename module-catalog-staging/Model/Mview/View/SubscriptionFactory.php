<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogStaging\Model\Mview\View;

use Magento\Framework\Mview\View\SubscriptionFactory as FrameworkSubscriptionFactory;

class SubscriptionFactory extends FrameworkSubscriptionFactory
{
    /**
     * @var array
     */
    private $stagingEntityTables = ['catalog_product_entity', 'catalog_category_entity'];

    /**
     * @var array
     */
    private $versionTables;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\CatalogStaging\Model\VersionTables $versionTables
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\CatalogStaging\Model\VersionTables $versionTables
    ) {
        parent::__construct($objectManager);
        $this->versionTables = $versionTables;
    }

    /**
     * @param array $data
     * @return \Magento\Framework\Mview\View\CollectionInterface
     */
    public function create(array $data = [])
    {
        if ($this->isStagingTable($data)) {
            $data['columnName'] = 'row_id';
        }
        return parent::create($data);
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function isStagingTable(array $data = [])
    {
        if (empty($data['tableName']) || in_array($data['tableName'], $this->stagingEntityTables)) {
            return false;
        }
        if (in_array($data['tableName'], $this->versionTables->getVersionTables())) {
            return true;
        }
        return false;
    }
}
