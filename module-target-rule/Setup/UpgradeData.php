<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @inheritdoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->resetActionSelectField($setup);
        }
    }

    /**
     * Reset 'action_select' field for each rule
     *
     * The 'action_select' field of 'magento_targetrule' table stores part of SELECT query.
     * This part combines with base part of SELECT query as hardcoded in getConditionForCollection() method
     * of class \Magento\TargetRule\Model\Actions\Condition\Product\Attributes.
     *
     * An 'action_select' field is a some kind of cache, which stores data unique for each rule.
     * Because the algorithm of producing this unique data (part of SELECT query) was changed,
     * the data already stored in DB must be cleaned (set to NULL).
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    public function resetActionSelectField(ModuleDataSetupInterface $setup)
    {
        $setup->getConnection()->update(
            $setup->getTable('magento_targetrule'),
            ['action_select' => null]
        );
    }
}
