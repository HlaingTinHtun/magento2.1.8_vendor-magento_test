<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogStaging\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade the CatalogStaging module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->addIndexIfNotExist(
            $setup,
            'catalog_product_entity',
            ['entity_id', 'created_in', 'updated_in']
        );
    }

    /**
     * Adds an index to a table when the index is not exists
     *
     * @param SchemaSetupInterface $setup
     * @param string $table
     * @param array $idxFields
     * @return void
     */
    private function addIndexIfNotExist(SchemaSetupInterface $setup, $table, $idxFields)
    {
        $idxName = $setup->getIdxName($table, $idxFields);

        $tableName = $setup->getTable($table);
        $tableIndexes = array_column($setup->getConnection()->getIndexList($tableName), 'KEY_NAME');
        $hasIndex = in_array($idxName, $tableIndexes, true);

        if (!$hasIndex) {
            $setup->getConnection()->addIndex($tableName, $idxName, $idxFields);
        }
    }
}
