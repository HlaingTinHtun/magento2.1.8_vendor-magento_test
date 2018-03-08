<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedSalesRule\Test\Unit\Model\Plugin;

/**
 * Class RuleTest
 */
class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedSalesRule\Model\Indexer\SalesRule\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerProcessorMock;

    /**
     * @var \Magento\AdvancedSalesRule\Model\Plugin\Rule
     */
    protected $model;

    /**
     * @var \Closure
     */
    protected $closureMock;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $className = '\Magento\AdvancedSalesRule\Model\Indexer\SalesRule\Processor';
        $this->indexerProcessorMock = $this->getMock($className, [], [], '', false);


        $this->model = $this->objectManager->getObject(
            'Magento\AdvancedSalesRule\Model\Plugin\Rule',
            [
                'indexerProcessor' => $this->indexerProcessorMock
            ]
        );
    }

    /**
     * test AroundSave when the sales rule is a new object
     */
    public function testAroundSaveNewObject()
    {
        $className = 'Magento\SalesRule\Model\Rule';
        /** @var \Magento\SalesRule\Model\Rule|\PHPUnit_Framework_MockObject_MockObject $subject */
        $subject = $this->getMock($className, [], [], '', false);

        $subject->expects($this->any())
            ->method('isObjectNew')
            ->willReturn(true);

        $subject->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $this->indexerProcessorMock->expects($this->once())
            ->method('reindexRow')
            ->with(1);

        $this->closureMock = function () use ($subject) {
            return $subject;
        };

        $this->assertSame($subject, $this->model->aroundSave($subject, $this->closureMock));
    }

    /**
     * test AroundSave when skip_save_filter flag is set
     */
    public function testAroundSaveSkipAfter()
    {
        $className = 'Magento\SalesRule\Model\Rule';
        /** @var \Magento\SalesRule\Model\Rule|\PHPUnit_Framework_MockObject_MockObject $subject */
        $subject = $this->getMock($className, [], [], '', false);

        $subject->expects($this->once())
            ->method('getData')
            ->with('skip_save_filter')
            ->willReturn(true);

        $subject->expects($this->never())
            ->method('isObjectNew');

        $this->indexerProcessorMock->expects($this->never())
            ->method('reindexRow');

        $this->closureMock = function () use ($subject) {
            return $subject;
        };

        $this->assertSame($subject, $this->model->aroundSave($subject, $this->closureMock));
    }

    /**
     * test AroundSave
     */
    public function testAfterSave()
    {
        $className = 'Magento\SalesRule\Model\Rule';
        /** @var \Magento\SalesRule\Model\Rule|\PHPUnit_Framework_MockObject_MockObject $subject */
        $subject =$this->getMock($className, [], [], '', false);

        $subject->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $className = 'Magento\AdvancedRule\Model\Condition\FilterableConditionInterface';
        /** @var  \Magento\AdvancedRule\Model\Condition\FilterableConditionInterface $conditions */
        $conditions = $this->getMock(
            $className,
            ['isFilterable', 'asArray', 'getFilterGroups'],
            [],
            '',
            false
        );
        $subject->expects($this->any())
            ->method('getConditions')
            ->willReturn($conditions);

        $conditions->expects($this->once())
            ->method('asArray')
            ->willReturn(['a' => 'b']);

        $subject->expects($this->once())
            ->method('isObjectNew')
            ->willReturn(false);
        $subject->expects($this->once())
            ->method('getOrigData')
            ->with('conditions_serialized')
            ->willReturn('abc');

        $this->indexerProcessorMock->expects($this->once())
            ->method('reindexRow')
            ->with(1);

        $this->closureMock = function () use ($subject) {
            return $subject;
        };

        $this->assertSame($subject, $this->model->aroundSave($subject, $this->closureMock));
    }

    /**
     * test AroundSave when force_save_filter flag is set
     */
    public function testAfterSaveForced()
    {
        $className = 'Magento\SalesRule\Model\Rule';
        /** @var \Magento\SalesRule\Model\Rule|\PHPUnit_Framework_MockObject_MockObject $subject */
        $subject =$this->getMock($className, [], [], '', false);

        $subject->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $className = 'Magento\AdvancedRule\Model\Condition\FilterableConditionInterface';
        /** @var  \Magento\AdvancedRule\Model\Condition\FilterableConditionInterface $conditions */
        $conditions = $this->getMock(
            $className,
            ['isFilterable', 'asArray', 'getFilterGroups'],
            [],
            '',
            false
        );
        $subject->expects($this->any())
            ->method('getConditions')
            ->willReturn($conditions);

        $subject->expects($this->once())
            ->method('isObjectNew')
            ->willReturn(false);

        $subject->expects($this->atLeastOnce())
            ->method('getData')
            ->willReturnCallback(
                function ($field) {
                    if ($field == 'skip_save_filter') {
                        return false;
                    } elseif ($field == 'force_save_filter') {
                        return true;
                    } else {
                        return true; // default
                    }
                }
            );

        $this->indexerProcessorMock->expects($this->once())
            ->method('reindexRow')
            ->with(1);

        $this->closureMock = function () use ($subject) {
            return $subject;
        };

        $this->assertSame($subject, $this->model->aroundSave($subject, $this->closureMock));
    }

    /**
     * test AroundSave when condition did not change
     */
    public function testAfterSaveConditionNotChanged()
    {
        $className = 'Magento\SalesRule\Model\Rule';
        /** @var \Magento\SalesRule\Model\Rule|\PHPUnit_Framework_MockObject_MockObject $subject */
        $subject =$this->getMock(
            $className,
            ['getConditionsSerialized', 'getOrigData', 'isObjectNew'],
            [],
            '',
            false
        );

        $serializedCondition = 'serialized';
        $subject->expects($this->once())
            ->method('isObjectNew')
            ->willReturn(false);
        $subject->expects($this->once())
            ->method('getConditionsSerialized')
            ->willReturn($serializedCondition);
        $subject->expects($this->once())
            ->method('getOrigData')
            ->with('conditions_serialized')
            ->willReturn($serializedCondition);

        $this->indexerProcessorMock->expects($this->never())
            ->method('reindexRow');

        $this->closureMock = function () use ($subject) {
            return $subject;
        };

        $this->assertSame($subject, $this->model->aroundSave($subject, $this->closureMock));
    }

    /**
     * test AroundSave when condition did not change
     */
    public function testAfterSaveConditionNotChangedNoSerializedCondition()
    {
        $className = 'Magento\SalesRule\Model\Rule';
        /** @var \Magento\SalesRule\Model\Rule|\PHPUnit_Framework_MockObject_MockObject $subject */
        $subject =$this->getMock(
            $className,
            ['getConditionsSerialized', 'getOrigData', 'isObjectNew', 'getConditions'],
            [],
            '',
            false
        );

        $this->setupConditionNotChanged($subject);

        $this->indexerProcessorMock->expects($this->never())
            ->method('reindexRow');

        $this->closureMock = function () use ($subject) {
            return $subject;
        };

        $this->assertSame($subject, $this->model->aroundSave($subject, $this->closureMock));
    }

    /**
     * Setup method for testAfterSaveConditionNotChangedNoSerializedCondition
     * @param \Magento\SalesRule\Model\Rule|\PHPUnit_Framework_MockObject_MockObject $subject
     */
    protected function setupConditionNotChanged($subject)
    {
        $conditionArray = ['a' => 'b'];
        $originalConditionArray = ['a' => 'b'];
        $conditionMock = $this->getMockBuilder('\Magento\Rule\Model\Condition\Combine')
            ->disableOriginalConstructor()
            ->setMethods(['asArray'])
            ->getMock();
        $conditionMock->expects($this->once())
            ->method('asArray')
            ->willReturn($conditionArray);

        $subject->expects($this->once())
            ->method('isObjectNew')
            ->willReturn(false);
        $subject->expects($this->once())
            ->method('getConditionsSerialized')
            ->willReturn(null);
        $subject->expects($this->once())
            ->method('getOrigData')
            ->with('conditions_serialized')
            ->willReturn(serialize($originalConditionArray));
        $subject->expects($this->once())
            ->method('getConditions')
            ->willReturn($conditionMock);
    }
}
