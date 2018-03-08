<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CmsStaging\Test\Unit\Controller\Adminhtml\Page\Update;

use Magento\CmsStaging\Controller\Adminhtml\Page\Update\Save as SaveController;

class SaveTest extends \PHPUnit_Framework_TestCase
{
    /** @var SaveController */
    protected $controller;

    /** @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /** @var \Magento\Staging\Model\Entity\Update\Save|\PHPUnit_Framework_MockObject_MockObject */
    protected $stagingUpdateSave;

    /** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    public function setUp()
    {
        $this->context = $this->getMockBuilder('Magento\Backend\App\Action\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = $this->getMockBuilder('Magento\Framework\App\RequestInterface')
            ->setMethods([
                'getPostValue',
                'getParam',
                'getModuleName',
                'setModuleName',
                'getActionName',
                'setActionName',
                'setParams',
                'getParams',
                'getCookie',
                'isSecure',
            ])
            ->getMock();
        $this->context->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->request);
        $this->stagingUpdateSave = $this->getMockBuilder('Magento\Staging\Model\Entity\Update\Save')
            ->disableOriginalConstructor()
            ->getMock();
        $this->controller = new SaveController($this->context, $this->stagingUpdateSave);
    }

    public function testExecute()
    {
        $pageId = 1;
        $entityData = [];
        $staging = [];
        $this->request->expects($this->exactly(2))
            ->method('getParam')
            ->withConsecutive(
                ['page_id'],
                ['staging']
            )
            ->willReturnOnConsecutiveCalls(
                $pageId,
                $staging
            );
        $this->request->expects($this->once())
            ->method('getPostValue')
            ->willReturn($entityData);
        $this->stagingUpdateSave
            ->expects($this->once())
            ->method('execute')
            ->with([
                'entityId' => $pageId,
                'stagingData' => $staging,
                'entityData' => $entityData
            ])
            ->willReturn(true);
        $this->assertTrue($this->controller->execute());
    }
}
