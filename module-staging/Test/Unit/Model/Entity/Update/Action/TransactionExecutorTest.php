<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Model\Entity\Update\Action;

class TransactionExecutorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $resourceConnection;

    /**
     * @var \Magento\Staging\Model\Entity\Update\Action\TransactionExecutor
     */
    protected $transactionExecutor;

    public function setUp()
    {
        $this->resourceConnection = $this->getMockBuilder('Magento\Framework\App\ResourceConnection')
            ->disableOriginalConstructor()
            ->getMock();
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->transactionExecutor = $objectManager->getObject(
            'Magento\Staging\Model\Entity\Update\Action\TransactionExecutor',
            [
                'resourceConnection' => $this->resourceConnection
            ]
        );
    }

    public function testSetAction()
    {
        $action = $this->getMockBuilder('Magento\Staging\Model\Entity\Update\Action\ActionInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertNull($this->transactionExecutor->setAction($action));
    }

    /**
     * @expectedException \LogicException
     */
    public function testExecuteNoAction()
    {
        $this->transactionExecutor->execute([]);
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuteRollback()
    {
        $action = $this->getMockBuilder('Magento\Staging\Model\Entity\Update\Action\ActionInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $action->expects($this->once())
            ->method('execute')
            ->with([])
            ->willThrowException(new \Exception('Error during save'));

        $adapterMock = $this->getMockBuilder('Magento\Framework\DB\Adapter\AdapterInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resourceConnection->expects($this->once())
            ->method('getConnection')
            ->willReturn($adapterMock);

        $adapterMock->expects($this->once())
            ->method('rollBack');

        $this->transactionExecutor->setAction($action);
        $this->transactionExecutor->execute([]);
    }

    public function testExecute()
    {
        $action = $this->getMockBuilder('Magento\Staging\Model\Entity\Update\Action\ActionInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $action->expects($this->once())
            ->method('execute')
            ->with([])
            ->willReturn(true);

        $adapterMock = $this->getMockBuilder('Magento\Framework\DB\Adapter\AdapterInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resourceConnection->expects($this->once())
            ->method('getConnection')
            ->willReturn($adapterMock);
        $adapterMock->expects($this->once())
            ->method('commit');

        $this->transactionExecutor->setAction($action);
        $this->transactionExecutor->execute([]);
    }
}
