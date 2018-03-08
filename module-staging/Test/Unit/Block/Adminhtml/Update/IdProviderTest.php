<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Block\Adminhtml\Update;

use Magento\Staging\Block\Adminhtml\Update\IdProvider;
use Magento\Framework\Exception\NoSuchEntityException;

class IdProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $updateRepositoryMock;

    /**
     * @var IdProvider
     */
    private $provider;

    protected function setUp()
    {
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->updateRepositoryMock = $this->getMock('Magento\Staging\Api\UpdateRepositoryInterface');

        $this->provider = new IdProvider(
            $this->requestMock,
            $this->updateRepositoryMock
        );
    }

    public function testGetUpdateIdReturnsIdIfUpdateExists()
    {
        $updateId = 1;
        $updateMock = $this->getMock('Magento\Staging\Api\Data\UpdateInterface');
        $updateMock->expects($this->any())->method('getId')->willReturn($updateId);
        $this->requestMock->expects($this->any())->method('getParam')->with('update_id')->willReturn($updateId);
        $this->updateRepositoryMock->expects($this->any())->method('get')->with($updateId)->willReturn($updateMock);

        $this->assertEquals($updateId, $this->provider->getUpdateId());
    }

    public function testGetUpdateIdReturnsNullIfUpdateDoesNotExist()
    {
        $updateId = 9999;
        $this->requestMock->expects($this->any())->method('getParam')->with('update_id')->willReturn($updateId);
        $this->updateRepositoryMock->expects($this->any())
            ->method('get')
            ->with($updateId)
            ->willThrowException(NoSuchEntityException::singleField('id', $updateId));

        $this->assertEquals(null, $this->provider->getUpdateId());
    }
}
