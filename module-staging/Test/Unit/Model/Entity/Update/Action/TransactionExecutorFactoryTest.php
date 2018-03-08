<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Model\Entity\Update\Action;

class TransactionExecutorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $objectManager;

    /** @var string */
    private $instanceName = '\\Magento\\Staging\\Model\\Entity\\Update\\Action\\TransactionExecutor';

    /**
     * @var \Magento\Staging\Model\Entity\Update\Action\TransactionExecutorFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->objectManager = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->factory = new \Magento\Staging\Model\Entity\Update\Action\TransactionExecutorFactory(
            $this->objectManager,
            $this->instanceName
        );
    }

    public function testCreate()
    {
        $executorMock = $this->getMockBuilder($this->instanceName)
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManager->expects($this->once())
            ->method('get')
            ->with($this->instanceName)
            ->willReturn($executorMock);
        $this->assertInstanceOf($this->instanceName, $this->factory->create());
    }
}
