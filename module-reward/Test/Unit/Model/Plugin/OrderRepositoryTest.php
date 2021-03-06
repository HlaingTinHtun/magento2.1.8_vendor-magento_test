<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Model\Plugin;

use Magento\Reward\Model\Plugin\OrderRepository;
use Magento\Sales\Model\Order;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class OrderRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrderRepository
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $orderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $orderRepositoryMock;

    protected function setUp()
    {
        $this->orderMock = $this->getMock(
            'Magento\Sales\Model\Order',
            [
                '__wakeup',
                'canUnhold',
                'isCanceled',
                'getState',
                'setForcedCanCreditmemo',
                'getBaseRwrdCrrncyAmntRefnded',
                'getBaseRwrdCrrncyAmtInvoiced',
            ],
            [],
            '',
            false
        );
        $this->orderRepositoryMock = $this->getMock('Magento\Sales\Api\OrderRepositoryInterface');
        $this->model = new OrderRepository();
    }

    /**
     * @param bool $canUnhold
     * @param bool $isCanceled
     * @param string $state
     * @dataProvider nonrefundableOrderStateDataProvider
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testAroundGetDoesNotForceCreditmemoIfOrderStateDoesNotAllowIt(
        $canUnhold,
        $isCanceled,
        $state,
        $rewardAmountInvoiced,
        $rewardAmountRefunded
    ) {
        $orderMock = $this->orderMock;
        $orderMock->expects($this->any())->method('canUnhold')->willReturn($canUnhold);
        $orderMock->expects($this->any())->method('isCanceled')->willReturn($isCanceled);
        $orderMock->expects($this->any())->method('getState')->willReturn($state);
        $orderMock->expects($this->any())->method('getBaseRwrdCrrncyAmtInvoiced')->willReturn($rewardAmountInvoiced);
        $orderMock->expects($this->any())->method('getBaseRwrdCrrncyAmntRefnded')->willReturn($rewardAmountRefunded);
        $orderMock->expects($this->never())->method('setForcedCanCreditmemo')->with(true);

        $closure = function ($orderId) use ($orderMock) {
            return $orderMock;
        };

        $this->assertEquals($orderMock, $this->model->aroundGet($this->orderRepositoryMock, $closure, 1));
    }

    /**
     * @return array
     */
    public function nonrefundableOrderStateDataProvider()
    {
        return [
            [false, false, Order::STATE_NEW, 10, 10],
            [false, false, Order::STATE_CLOSED, 20, 10],
            [false, true, Order::STATE_CLOSED, 10, 10],
            [true, false, Order::STATE_CLOSED, 20, 10],
            [true, true, Order::STATE_CLOSED, 10, 10],
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testAroundGetForcesCreditmemoIfOrderStateAllowsIt()
    {
        $orderMock = $this->orderMock;
        $orderMock->expects($this->any())->method('canUnhold')->willReturn(false);
        $orderMock->expects($this->any())->method('isCanceled')->willReturn(false);
        $orderMock->expects($this->any())->method('getState')->willReturn(Order::STATE_NEW);
        $orderMock->expects($this->any())->method('getBaseRwrdCrrncyAmtInvoiced')->willReturn(100);
        $orderMock->expects($this->any())->method('getBaseRwrdCrrncyAmntRefnded')->willReturn(50);

        $orderMock->expects($this->once())->method('setForcedCanCreditmemo')->with(true);

        $closure = function ($orderId) use ($orderMock) {
            return $orderMock;
        };

        $this->assertEquals($orderMock, $this->model->aroundGet($this->orderRepositoryMock, $closure, 1));
    }
}
