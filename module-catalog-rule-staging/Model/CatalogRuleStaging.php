<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogRuleStaging\Model;

use Magento\CatalogRuleStaging\Api\CatalogRuleStagingInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\CatalogRule\Api\Data\RuleInterface;
use Magento\Staging\Model\ResourceModel\Db\CampaignValidator;
use Magento\Framework\Exception\ValidatorException;

/**
 * Class SalesRuleStaging
 */
class CatalogRuleStaging implements CatalogRuleStagingInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CampaignValidator
     */
    private $campaignValidator;

    /**
     * CatalogRuleStaging constructor.
     *
     * @param CampaignValidator $campaignValidator
     * @param EntityManager $entityManager
     */
    public function __construct(
        CampaignValidator $campaignValidator,
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->campaignValidator = $campaignValidator;
    }

    /**
     * @param RuleInterface $catalogRule
     * @param string $version
     * @param array $arguments
     * @return bool
     * @throws \Exception
     */
    public function schedule(\Magento\CatalogRule\Api\Data\RuleInterface $catalogRule, $version, $arguments = [])
    {
        $previous = isset($arguments['origin_in']) ? $arguments['origin_in'] : null;
        if (!$this->campaignValidator->canBeScheduled($catalogRule, $version, $previous)) {
            throw new ValidatorException(
                __(
                    'Future Update in this time range already exists. '
                    . 'Select a different range to add a new Future Update.'
                )
            );
        }
        $arguments['created_in'] = $version;
        return (bool)$this->entityManager->save($catalogRule, $arguments);
    }

    /**
     * @param \Magento\CatalogRule\Api\Data\RuleInterface $catalogRule
     * @param string $version
     * @return bool
     */
    public function unschedule(\Magento\CatalogRule\Api\Data\RuleInterface $catalogRule, $version)
    {
        return (bool)$this->entityManager->delete(
            $catalogRule,
            [
                'created_in' => $version
            ]
        );
    }
}
