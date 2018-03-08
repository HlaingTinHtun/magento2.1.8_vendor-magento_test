<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Block\Adminhtml\Update\Entity;

use Magento\Staging\Block\Adminhtml\Update\Entity\Toolbar;

class ToolbarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var Toolbar
     */
    protected $toolbar;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $buttonListMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $toolbarMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Backend\Block\Widget\Context', [], [], '', false);

        $this->buttonListMock = $this->getMock(
            '\Magento\Backend\Block\Widget\Button\ButtonList',
            [],
            [],
            '',
            false
        );
        $this->contextMock->expects($this->atLeastOnce())
            ->method('getButtonList')
            ->willReturn($this->buttonListMock);

        $this->toolbarMock = $this->getMock(
            'Magento\Backend\Block\Widget\Button\ToolbarInterface',
            [],
            [],
            '',
            false
        );
        $this->contextMock->expects($this->once())
            ->method('getButtonToolbar')
            ->willReturn($this->toolbarMock);
        $this->data = [];
        $this->toolbar = new Toolbar(
            $this->contextMock,
            $this->data
        );
    }

    public function testUpdateButton()
    {
        $buttonId = '123';
        $key = '456';
        $data = '2341';
        $this->buttonListMock->expects($this->once())
            ->method('update')
            ->with($buttonId, $key, $data);
        $this->toolbar->updateButton($buttonId, $key, $data);
    }

    public function testPrepareLayout()
    {
        $layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface');
        $this->buttonListMock->expects($this->once())->method('add');
        $this->toolbarMock->expects($this->once())->method('pushButtons');
        $this->toolbar->setLayout($layoutMock);
    }

    public function testAddButton()
    {
        $buttonId = 'LuckyId';
        $data = [300, 20, 30];
        $level = 100;
        $sortOrder = 330;
        $region = 'SomePlace';


        $this->buttonListMock->expects($this->once())
            ->method('add')
            ->with($buttonId, $data, $level, $sortOrder, $region);
        $this->toolbar->addButton($buttonId, $data, $level, $sortOrder, $region);
    }

    public function testRemoveButton()
    {
        $buttonId = 'HopHey';

        $this->buttonListMock->expects($this->once())
            ->method('remove')
            ->with($buttonId);
        $this->toolbar->removeButton($buttonId);
    }

    public function testCanRender()
    {
        $itemMock = $this->getMock('\Magento\Backend\Block\Widget\Button\Item', [], [], '', false);
        $itemMock->expects($this->once())->method('isDeleted')->willReturn(true);
        $this->assertEquals(false, $this->toolbar->canRender($itemMock));
    }
}
