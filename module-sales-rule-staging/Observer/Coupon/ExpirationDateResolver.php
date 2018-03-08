<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRuleStaging\Observer\Coupon;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Magento\SalesRule\Api\Data\CouponInterface;
use Magento\SalesRule\Api\Data\CouponSearchResultInterface;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Updating coupon expiration date according to applied sales rule update.
 */
class ExpirationDateResolver implements ObserverInterface
{
    /**
     * Coupon CRUD interface.
     *
     * @var CouponRepositoryInterface
     */
    private $couponRepository;

    /**
     * Filter Builder.
     *
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * Search Criteria Builder.
     *
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * Sales rule CRUD interface.
     *
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * Describes a logger instance.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ExpirationDateResolver constructor.
     *
     * @param CouponRepositoryInterface $couponRepository
     * @param FilterBuilder             $filterBuilder
     * @param SearchCriteriaBuilder     $criteriaBuilder
     * @param RuleRepositoryInterface   $ruleRepository
     * @param LoggerInterface           $logger
     */
    public function __construct(
        CouponRepositoryInterface $couponRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $criteriaBuilder,
        RuleRepositoryInterface $ruleRepository,
        LoggerInterface $logger
    ) {
        $this->couponRepository = $couponRepository;
        $this->filterBuilder = $filterBuilder;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->ruleRepository = $ruleRepository;
        $this->logger = $logger;
    }

    /**
     * Updating coupon expiration date according to applied sales rule update.
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var array $ids */
        $ids = $observer->getData('entity_ids');

        //load coupon collection that matched ids
        $this->criteriaBuilder->addFilters(
            [
                $this->filterBuilder->setField('rule_id')
                    ->setValue($ids)->setConditionType('in')->create(),
            ]
        );

        /** @var SearchCriteria $searchCriteria */
        $searchCriteria = $this->criteriaBuilder->create();

        /** @var CouponSearchResultInterface $coupons */
        $coupons = $this->couponRepository->getList($searchCriteria);

        /** @var CouponInterface $coupon */
        foreach ($coupons->getItems() as $coupon) {
            try {
                /** @var string $ruleId */
                $ruleId = $coupon->getRuleId();
                /** @var  RuleInterface $rule */
                $rule = $this->ruleRepository->getById($ruleId);
                $coupon->setExpirationDate($rule->getToDate());
                $this->couponRepository->save($coupon);
            } catch (\Exception $e) {
                //do nothing continue with updating other coupons
                $message = __(
                    'An error occurred during processing; coupon with id %1 expiration date'
                    . ' wasn\'t updated. The error message was: %2',
                    $coupon->getCouponId(),
                    $e->getMessage()
                );

                $this->logger->error($message);
            }
        }
    }
}
