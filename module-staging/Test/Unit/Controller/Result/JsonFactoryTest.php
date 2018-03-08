<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Controller\Result;

use Magento\Staging\Controller\Result\JsonFactory;

class JsonFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var JsonFactory
     */
    protected $factory;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface', [], [], '', false);
        $this->messageManagerMock = $this->getMock('Magento\Framework\Message\ManagerInterface', [], [], '', false);

        $this->factory = new JsonFactory($this->objectManagerMock, $this->messageManagerMock);

    }

    public function testCreate()
    {
        $messageText = 'Some message';
        $messages = $messageText . '<br/>' . $messageText . '<br/>';

        $messageCollectionMock = $this->getMock('Magento\Framework\Message\Collection', [], [], '', false);

        $jsonMock = $this->getMock('Magento\Framework\Controller\Result\Json', [], [], '', false);
        $this->objectManagerMock->expects($this->once())->method('create')->willReturn($jsonMock);

        $this->messageManagerMock->expects($this->once())
            ->method('getMessages')
            ->with(true)
            ->willReturn($messageCollectionMock);
        $messageMock = $this->getMock('Magento\Framework\Message\MessageInterface');
        $items = [$messageMock, $messageMock];
        $messageCollectionMock->expects($this->once())->method('getItems')->willReturn($items);

        $messageMock->expects($this->exactly(2))->method('toString')->willReturn($messageText);

        $jsonMock->expects($this->once())->method('setData')->with(['messages' => $messages]);
        $this->assertEquals($jsonMock, $this->factory->create());
    }
}
