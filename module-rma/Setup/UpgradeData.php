<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Rma\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * Sales setup factory
     *
     * @var RmaSetupFactory
     */
    protected $rmaSetupFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @param RmaSetupFactory $rmaSetupFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        RmaSetupFactory $rmaSetupFactory,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->rmaSetupFactory = $rmaSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var RmaSetup $rmaSetup */
        $rmaSetup = $this->rmaSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $rmaSetup->updateEntityType(
                \Magento\Rma\Model\Item::ENTITY,
                'entity_model',
                'Magento\Rma\Model\ResourceModel\Item'
            );
            $rmaSetup->updateEntityType(
                \Magento\Rma\Model\Item::ENTITY,
                'increment_model',
                'Magento\Eav\Model\Entity\Increment\NumericValue'
            );
        }

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $rmaSetup->addAttributeToGroup(Product::ENTITY, 'Default', 'Product Details', 'is_returnable', 120);
        }

        $this->eavConfig->clear();
        $setup->endSetup();
    }
}
