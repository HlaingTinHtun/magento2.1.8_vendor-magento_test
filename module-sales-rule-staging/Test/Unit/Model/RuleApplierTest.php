<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRuleStaging\Test\Unit\Model;

/**
 * Class RuleApplierTest
 */
class RuleApplierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRuleStaging\Model\RuleApplier
     */
    protected $model;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $className = '\Magento\SalesRuleStaging\Model\RuleApplier';

        $this->eventManagerMock = $this->getMock('\Magento\Framework\Event\ManagerInterface');

        $this->model = $objectManager->getObject(
            $className,
            [
                'eventManager' => $this->eventManagerMock,
            ]
        );
    }

    public function testExecuteEmpty()
    {
        $ids = [];
        $this->eventManagerMock->expects($this->never())
            ->method('dispatch');
        $this->model->execute($ids);
    }

    public function testExecute()
    {
        $ids = [1, 2];
        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with('sales_rule_updated', ['entity_ids' => $ids]);
        $this->model->execute($ids);
    }
}
