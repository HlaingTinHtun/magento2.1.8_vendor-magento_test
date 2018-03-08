<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Model\Entity\Update\Action;

use Magento\Framework\ObjectManagerInterface;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    private $actions = [
        'Magento\SalesRule\Api\Data\RuleInterface' => [
            'save' => [
                'save' => 'ruleUpdateSaveSaveAction',
                'assign' => 'ruleUpdateSaveAssignAction',
            ]
        ]
    ];

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $transactionPool;

    /** @var ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    private $objectManager;

    /**
     * @var \Magento\Staging\Model\Entity\Update\Action\Pool
     */
    protected $pool;

    public function setUp()
    {
        $this->transactionPool = $this->getMockBuilder('Magento\Staging\Model\Entity\Update\Action\TransactionPool')
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManager = $this->getMockBuilder(ObjectManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->pool = new \Magento\Staging\Model\Entity\Update\Action\Pool(
            $this->transactionPool,
            $this->objectManager,
            $this->actions
        );
    }

    public function testGetExecutorNotExistsInPool()
    {
        $action = $this->getMockBuilder('Magento\Staging\Model\Entity\Update\Action\ActionInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertEquals($action, $this->pool->getExecutor($action));
    }

    public function testGetExecutor()
    {
        $action = $this->getMockBuilder('Magento\Staging\Model\Entity\Update\Action\ActionInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $executor = $this->getMockBuilder('Magento\Staging\Model\Entity\Update\Action\TransactionExecutorInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $executor->expects($this->once())
            ->method('setAction');
        $this->transactionPool->expects($this->once())
            ->method('getExecutor')
            ->willReturn($executor);
        $this->assertEquals($executor, $this->pool->getExecutor($action));
    }

    public function testGetAction()
    {
        $entityType = 'Magento\SalesRule\Api\Data\RuleInterface';
        $namespace = 'save';
        $actionType = 'assign';
        $action = $this->getMockBuilder('Magento\Staging\Model\Entity\Update\Action\ActionInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManager->expects($this->once())
            ->method('get')
            ->with($this->actions[$entityType][$namespace][$actionType])->willReturn($action);
        $this->assertEquals($action, $this->pool->getAction($entityType, $namespace, $actionType));
    }
}
