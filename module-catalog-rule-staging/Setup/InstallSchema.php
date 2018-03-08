<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogRuleStaging\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var \Magento\CatalogRuleStaging\Setup\CatalogRuleSetupFactory
     */
    protected $catalogRuleSetupFactory;

    /**
     * @var \Magento\Staging\Setup\BasicSetup
     */
    protected $basicSetup;

    /**
     * @param CatalogRuleSetupFactory $catalogRuleSetupFactory
     * @param \Magento\Staging\Setup\BasicSetup $basicSetup
     */
    public function __construct(
        \Magento\CatalogRuleStaging\Setup\CatalogRuleSetupFactory $catalogRuleSetupFactory,
        \Magento\Staging\Setup\BasicSetup $basicSetup
    ) {
        $this->catalogRuleSetupFactory = $catalogRuleSetupFactory;
        $this->basicSetup = $basicSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->basicSetup->install(
            $setup,
            'sequence_catalogrule',
            'catalogrule',
            'rule_id',
            [
                [
                    'referenceTable' => 'catalogrule_customer_group',
                    'referenceColumn' => 'rule_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'catalogrule_group_website',
                    'referenceColumn' => 'rule_id',
                    'staged' => false
                ],
                [
                    'referenceTable' => 'catalogrule_website',
                    'referenceColumn' => 'rule_id',
                    'staged' => true
                ]
            ]
        );

        /** @var \Magento\CatalogRuleStaging\Setup\CatalogRuleSetup $catalogRuleSetup */
        $catalogRuleSetup = $this->catalogRuleSetupFactory->create();

        // Migrate catalog rules for staging
        $catalogRuleSetup->migrateRules($setup);

        $setup->endSetup();
    }
}
