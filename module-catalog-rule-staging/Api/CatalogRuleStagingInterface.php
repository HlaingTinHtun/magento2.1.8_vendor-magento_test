<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogRuleStaging\Api;

/**
 * Interface CatalogRuleStagingInterface
 * @api
 */
interface CatalogRuleStagingInterface
{
    /**
     * @param \Magento\CatalogRule\Api\Data\RuleInterface $catalogRule
     * @param string $version
     * @param array $arguments
     * @return bool
     */
    public function schedule(\Magento\CatalogRule\Api\Data\RuleInterface $catalogRule, $version, $arguments = []);

    /**
     * @param \Magento\CatalogRule\Api\Data\RuleInterface $catalogRule
     * @param string $version
     * @return bool
     */
    public function unschedule(\Magento\CatalogRule\Api\Data\RuleInterface $catalogRule, $version);
}
