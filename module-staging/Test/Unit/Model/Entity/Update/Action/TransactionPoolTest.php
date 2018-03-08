<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Model\Entity\Update\Action;

class TransactionPoolTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $transactionFactory;

    /**
     * @var \Magento\Staging\Model\Entity\Update\Action\TransactionPool
     */
    private $transactionPool;

    public function setUp()
    {
        $transactionFactory = 'Magento\Staging\Model\Entity\Update\Action\TransactionExecutorFactory';
        $this->transactionFactory = $this->getMockBuilder($transactionFactory)
            ->disableOriginalConstructor()
            ->getMock();
        $poolData = ['item1' => 'ActionObject'];
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->transactionPool = $objectManager->getObject(
            'Magento\Staging\Model\Entity\Update\Action\TransactionPool',
            [
                'transactionExecutorFactory' => $this->transactionFactory,
                'transactionPool' => $poolData
            ]
        );
    }

    public function testGetExecutor()
    {
        $namespace = 'ActionObject';
        $executor = 'Magento\Staging\Model\Entity\Update\Action\TransactionExecutorInterface';
        $transactionExecutor = $this->getMockBuilder($executor)
            ->disableOriginalConstructor()
            ->getMock();
        $this->transactionFactory->expects($this->once())
            ->method('create')
            ->willReturn($transactionExecutor);
        $this->assertInstanceOf($executor, $this->transactionPool->getExecutor($namespace));
    }
}
