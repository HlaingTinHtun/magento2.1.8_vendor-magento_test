<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CmsStaging\Test\Unit\Model\ResourceModel\Grid\Collection;

use Magento\CmsStaging\Model\ResourceModel\Grid\Collection\UpdatePlugin;
use Magento\Framework\Exception\NoSuchEntityException;

class UpdatePluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdatePlugin
     */
    private $plugin;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var \Magento\Staging\Api\UpdateRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $updateRepositoryMock;

    protected function setUp()
    {
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->updateRepositoryMock = $this->getMock('Magento\Staging\Api\UpdateRepositoryInterface');

        $this->plugin = new UpdatePlugin(
            $this->requestMock,
            $this->updateRepositoryMock
        );
    }

    /**
     * @param int|null $updateId
     * @param int $requestUpdateId
     * @param bool $isUpdateExists
     * @dataProvider getUpdateDataProvider
     */
    public function testBeforeGetItems($updateId, $requestUpdateId, $isUpdateExists)
    {
        $selectMock = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->disableOriginalConstructor()
            ->getMock();

        $collectionMock = $this->getMockBuilder('Magento\Cms\Model\ResourceModel\AbstractCollection')
            ->setMethods(['getSelect'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $collectionMock->expects($this->any())
            ->method('getSelect')
            ->willReturn($selectMock);

        $updateMock = $this->getMock('Magento\Staging\Model\Update', [], [], '', false);
        $updateMock->expects($this->any())->method('getId')->willReturn($isUpdateExists ? $updateId : false);

        $this->requestMock->expects($this->any())->method('getParam')->willReturn($requestUpdateId);
        $this->updateRepositoryMock->expects($this->any())->method('get')->with($updateId)->willReturn($updateMock);

        if ($isUpdateExists) {
            $selectMock->expects($this->once())->method('setPart')->with('disable_staging_preview', true);
        } else {
            $selectMock->expects($this->never())->method('setPart');
        }

        $this->plugin->beforeGetItems($collectionMock);
    }

    /**
     * Update data provider
     *
     * @return array
     */
    public function getUpdateDataProvider()
    {
        return [
            [1, 1, true],//update exists
            [123, 123, false],//update does not exist
        ];
    }
}
