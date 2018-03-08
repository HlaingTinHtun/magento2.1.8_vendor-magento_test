<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRuleStaging\Model\Plugin;

use Magento\SalesRule\Model\Rule as RuleModel;
use Magento\Staging\Api\Data\UpdateInterface;
use Magento\Staging\Api\UpdateRepositoryInterface;

/**
 * Date resolver plugin on update salesrule.
 */
class DateResolverPlugin
{
    /**
     * Start date attribute.
     *
     * @var string
     */
    private static $startDateAttribute = 'from_date';

    /**
     * End date attribute.
     *
     * @var string
     */
    private static $endDataAttribute = 'to_date';

    /**
     * Update repository interface.
     *
     * @var UpdateRepositoryInterface
     */
    protected $updateRepository;

    /**
     * @param UpdateRepositoryInterface $updateRepository
     */
    public function __construct(
        UpdateRepositoryInterface $updateRepository
    ) {
        $this->updateRepository = $updateRepository;
    }

    /**
     * Provide update start date to the rule model.
     *
     * @param RuleModel $subject
     *
     * @return void
     */
    public function beforeGetFromDate(RuleModel $subject)
    {
        // for update end date if different than created_in
        $resolvedTime = new \DateTime($this->resolveDate($subject)->getStartTime());
        if ($resolvedTime != $subject->getData(self::$startDateAttribute)) {
            $subject->setData(
                self::$startDateAttribute,
                $resolvedTime->format('Y-m-d')
            );
        }
    }

    /**
     * Provide update end date to the rule model.
     *
     * @param RuleModel $subject
     *
     * @return void
     */
    public function beforeGetToDate(RuleModel $subject)
    {
        /* for update end date if different than updated_in */

        /** @var string|null $endTime */
        $endTime = $this->resolveDate($subject)->getEndTime();
        /** @var \DateTime|null $resolvedTime */
        $resolvedTime = $endTime !== null ? new \DateTime($endTime) : $endTime;
        /** @var \DateTime|string|null $toDate */
        $toDate = $subject->getData(self::$endDataAttribute);

        if ($toDate instanceof \DateTime) {
            $subject->setData(
                self::$endDataAttribute,
                $toDate->format('Y-m-d')
            );
        }

        if ($resolvedTime !== null && $resolvedTime->format('Y-m-d') != $toDate) {
            $subject->setData(
                self::$endDataAttribute,
                $resolvedTime->format('Y-m-d')
            );
        }
    }

    /**
     * Resolve date using update id.
     *
     * @param RuleModel $subject
     *
     * @return UpdateInterface
     */
    protected function resolveDate(RuleModel $subject)
    {
        /** @var string|null $campaignId */
        $campaignId = $subject->getData('campaign_id');
        /** @var string $versionId */
        $versionId = $campaignId === null ? $subject->getData('created_in') : $campaignId;

        return $this->updateRepository->get($versionId);
    }
}
