<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Test\Unit\Model\Segment\Condition\ConcreteCondition;

use Magento\CustomerSegment\Model\Segment\Condition\ConcreteCondition\Factory;
use Magento\CustomerSegment\Model\Segment\Condition\Segment;

/**
 * Class FactoryTest
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var Segment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $segmentCondition;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $concreteCondition;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerInterface;

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

        $className = '\Magento\CustomerSegment\Model\Segment\Condition\Segment';
        $this->segmentCondition = $this->getMock($className, ['getOperator', 'getValue'], [], '', false);

        $className = Factory::CONCRETE_CONDITION_CLASS;
        $this->concreteCondition = $this->getMock($className, [], [], '', false);

        $className = '\Magento\Framework\ObjectManagerInterface';
        $this->objectManagerInterface = $this->getMock($className, [], [], '', false);

        $className = '\Magento\CustomerSegment\Model\Segment\Condition\ConcreteCondition\Factory';
        $this->factory = $this->objectManager->getObject(
            $className,
            [
                'objectManager' => $this->objectManagerInterface,
            ]
        );
    }

    /**
     * test create
     */
    public function testCreate()
    {
        $this->segmentCondition->expects($this->once())
            ->method('getOperator')
            ->willReturn('==');
        $this->segmentCondition->expects($this->once())
            ->method('getValue')
            ->willReturn('1');

        $this->objectManagerInterface->expects($this->once())
            ->method('create')
            ->with(Factory::CONCRETE_CONDITION_CLASS)
            ->willReturn($this->concreteCondition);

        $result = $this->factory->create($this->segmentCondition);
        $this->assertEquals($this->concreteCondition, $result);
    }
}
