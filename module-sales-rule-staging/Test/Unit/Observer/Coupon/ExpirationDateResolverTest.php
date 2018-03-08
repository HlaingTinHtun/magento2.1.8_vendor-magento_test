<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRuleStaging\Test\Unit\Observer\Coupon;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Phrase;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Magento\SalesRule\Api\Data\CouponInterface;
use Magento\SalesRule\Api\Data\CouponSearchResultInterface;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRuleStaging\Observer\Coupon\ExpirationDateResolver;
use Psr\Log\LoggerInterface;

/**
 * Tests \Magento\SalesRuleStaging\Observer\Coupon\ExpirationDateResolver class.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ExpirationDateResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CouponRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponRepositoryMock;

    /**
     * Filter Builder
     *
     * @var FilterBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterBuilderMock;

    /**
     * Search Criteria Builder
     *
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $criteriaBuilderMock;

    /**
     * Sales rule CRUD interface.
     *
     * @var RuleRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleRepositoryMock;

    /**
     * Describes a logger instance.
     *
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * @var ExpirationDateResolver
     */
    private $model;

    /**
     * @var Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $observerMock;

    protected function setUp()
    {
        $this->couponRepositoryMock = $this->getMockBuilder(CouponRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var array $methods */
        $methods = ['setField', 'setValue', 'setConditionType', 'create'];

        $this->filterBuilderMock = $this->getMockBuilder(FilterBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();

        $this->criteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->ruleRepositoryMock = $this->getMockBuilder(RuleRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->observerMock = $this->getMockBuilder(Observer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = new ExpirationDateResolver(
            $this->couponRepositoryMock,
            $this->filterBuilderMock,
            $this->criteriaBuilderMock,
            $this->ruleRepositoryMock,
            $this->loggerMock
        );
    }

    public function testExecute()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $couponMock */
        $couponMock = $this->prepareCouponMock();

        $this->couponRepositoryMock->expects(self::once())
            ->method('save')
            ->with($couponMock);

        $this->model->execute($this->observerMock);
    }

    public function testExecuteIfExceptionWasThrown()
    {
        /** @var int $couponId */
        $couponId = 2;

        /** @var \Exception $exception */
        $exception = new \Exception('MessageText');

        /** @var Phrase $message */
        $message = __(
            'An error occurred during processing; coupon with id %1 expiration date'
            . ' wasn\'t updated. The error message was: %2',
            $couponId,
            $exception->getMessage()
        );

        /** @var \PHPUnit_Framework_MockObject_MockObject $couponMock */
        $couponMock = $this->prepareCouponMock();
        $couponMock->expects(self::once())
            ->method('getCouponId')
            ->willReturn($couponId);

        $this->couponRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($couponMock)
            ->willThrowException($exception);

        $this->loggerMock->expects(self::once())
            ->method('error')
            ->with($message);

        $this->model->execute($this->observerMock);
    }

    /**
     * Prepare the coupon mock for test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareCouponMock()
    {
        /** @var int $ruleId */
        $ruleId = 1;

        /** @var Filter|\PHPUnit_Framework_MockObject_MockObject $filterMock */
        $filterMock = $this->getMockBuilder(Filter::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var SearchCriteria|\PHPUnit_Framework_MockObject_MockObject $searchCriteria */
        $searchCriteria = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var CouponInterface|\PHPUnit_Framework_MockObject_MockObject $couponMock */
        $couponMock = $this->getMockBuilder(CouponInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $couponMock->expects(self::once())
            ->method('getRuleId')
            ->willReturn($ruleId);
        $couponMock->expects(self::once())
            ->method('setExpirationDate')
            ->with('2016-09-20');

        /** @var CouponSearchResultInterface|\PHPUnit_Framework_MockObject_MockObject $searchResult */
        $searchResult = $this->getMockBuilder(CouponSearchResultInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $searchResult->expects(self::once())
            ->method('getItems')
            ->willReturn([$couponMock]);

        /** @var RuleInterface|\PHPUnit_Framework_MockObject_MockObject $ruleMock */
        $ruleMock = $this->getMockBuilder(RuleInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $ruleMock->expects(self::once())
            ->method('getToDate')
            ->willReturn('2016-09-20');

        $this->observerMock->expects(self::once())
            ->method('getData')
            ->with('entity_ids')
            ->willReturn([$ruleId]);

        $this->filterBuilderMock->expects(self::once())
            ->method('setField')
            ->willReturnSelf();
        $this->filterBuilderMock->expects(self::once())
            ->method('setValue')
            ->with([$ruleId])
            ->willReturnSelf();
        $this->filterBuilderMock->expects(self::once())
            ->method('setConditionType')
            ->willReturnSelf();
        $this->filterBuilderMock->expects(self::once())
            ->method('create')
            ->willReturn($filterMock);

        $this->criteriaBuilderMock->expects(self::once())
            ->method('addFilters')
            ->with([$filterMock]);
        $this->criteriaBuilderMock->expects(self::once())
            ->method('create')
            ->willReturn($searchCriteria);

        $this->couponRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($searchResult);

        $this->ruleRepositoryMock->expects(self::once())
            ->method('getById')
            ->with($ruleId)
            ->willReturn($ruleMock);

        return $couponMock;
    }
}
