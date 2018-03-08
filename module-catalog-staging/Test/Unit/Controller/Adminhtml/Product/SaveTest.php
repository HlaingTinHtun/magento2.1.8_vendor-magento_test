<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogStaging\Test\Unit\Controller\Adminhtml\Product;

use Magento\CatalogStaging\Controller\Adminhtml\Product\Save as SaveController;

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

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

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
        $this->storeManager = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->setMethods(['getCode', 'setCurrentStore', 'getStore'])
            ->getMockForAbstractClass();
        $this->controller = new SaveController($this->context, $this->stagingUpdateSave, $this->storeManager);
    }

    public function testExecute()
    {
        $productId = 1;
        $entityData = [];
        $staging = [];
        $this->request->expects($this->exactly(2))
            ->method('getParam')
            ->withConsecutive(
                ['id'],
                ['staging']
            )
            ->willReturnOnConsecutiveCalls(
                $productId,
                $staging
            );
        $this->request->expects($this->once())
            ->method('getPostValue')
            ->willReturn($entityData);
        $this->stagingUpdateSave
            ->expects($this->once())
            ->method('execute')
            ->with([
                'entityId' => $productId,
                'stagingData' => $staging,
                'entityData' => $entityData
            ])
            ->willReturn(true);
        $this->assertTrue($this->controller->execute());
    }
}
