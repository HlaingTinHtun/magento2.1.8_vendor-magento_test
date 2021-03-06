<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reminder\Test\Unit\Observer;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class DetachUnsupportedSalesRuleObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reminder\Observer\DetachUnsupportedSalesRuleObserver
     */
    private $model;

    /**
     * @var \Magento\Reminder\Model\RuleFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleFactory;

    /**
     * @var \Magento\Reminder\Model\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rule;

    /**
     * @var \Magento\Framework\Event\Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventObserver;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    private $salesRule;

    /**
     * @return void
     */
    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->reminderData = $this->getMockBuilder('Magento\Reminder\Helper\Data')
            ->setMethods(['isEnabled'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->rule = $this->getMockBuilder('Magento\Reminder\Model\Rule')
            ->setMethods(['sendReminderEmails', '__wakeup', 'detachSalesRule'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->salesRule = $this->getMockBuilder('Magento\SalesRule\Model\ResourceModel\Rule')
            ->setMethods(['getCouponType', 'getUseAutoGeneration', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->ruleFactory = $this->getMockBuilder('Magento\Reminder\Model\RuleFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->ruleFactory->expects($this->any())->method('create')->will($this->returnValue($this->rule));

        $this->eventObserver = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->setMethods(['getCollection', 'getRule', 'getForm', 'getEvent'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $helper->getObject(
            'Magento\Reminder\Observer\DetachUnsupportedSalesRuleObserver',
            ['ruleFactory' => $this->ruleFactory]
        );
    }

    /**
     * @return void
     */
    public function testDetachUnsupportedSalesRule()
    {
        $this->salesRule
            ->expects($this->once())
            ->method('getCouponType')
            ->willReturn(\Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC);
        $this->salesRule->expects($this->once())->method('getUseAutoGeneration')->willReturn([1]);
        $this->salesRule->expects($this->once())->method('getId')->willReturn(1);
        $this->rule->expects($this->once())->method('detachSalesRule')->with(1);
        $this->eventObserver->expects($this->once())->method('getRule')->willReturn($this->salesRule);
        $this->model->execute($this->eventObserver);
    }
}
