<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdminGws\Test\Unit\Model\Plugin;

class StoreManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdminGws\Model\Plugin\StoreManager
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $roleMock;

    protected function setUp()
    {
        $this->roleMock = $this->getMock(\Magento\AdminGws\Model\Role::class, [], [], '', false);
        $this->model = new \Magento\AdminGws\Model\Plugin\StoreManager($this->roleMock);
    }

    public function testAfterGetWebsitesIsNotRestricted()
    {
        $websiteMock =  $this->getMock(\Magento\Store\Model\Website::class, ['getWebsiteId'], [], '', false);
        $websiteMock->expects($this->never())->method('getWebsiteId');
        $result = [0 => $websiteMock, 1 => $websiteMock];
        $subjectMock = $this->getMock(\Magento\Store\Model\StoreManagerInterface::class, [], [], '', false);
        $this->roleMock->expects($this->once())->method('getIsAll')->will($this->returnValue(true));

        $this->assertEquals($result, $this->model->afterGetWebsites($subjectMock, $result));
    }

    public function testAfterGetWebsitesIsRestricted()
    {
        $websiteMock =  $this->getMock(\Magento\Store\Model\Website::class, ['getWebsiteId'], [], '', false);
        $websiteMock->expects($this->exactly(2))->method('getWebsiteId')->will($this->onConsecutiveCalls(0, 1));
        $result = [0 => $websiteMock, 1 => $websiteMock];
        $subjectMock = $this->getMock(\Magento\Store\Model\StoreManagerInterface::class, [], [], '', false);
        $this->roleMock->expects($this->once())->method('getIsAll')->will($this->returnValue(false));
        $this->roleMock->expects($this->exactly(2))->method('getRelevantWebsiteIds')->will($this->returnValue([0]));

        $expected = [0 => $websiteMock];
        $this->assertEquals($expected, $this->model->afterGetWebsites($subjectMock, $result));
    }
}
