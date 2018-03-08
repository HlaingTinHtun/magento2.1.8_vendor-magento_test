<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdminGws\Test\Unit\Model\Plugin;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test class for \Magento\AdminGws\Model\Plugin\SaveRole testing
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveRoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdminGws\Model\Plugin\SaveRole
     */
    protected $model;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $requestMock;

    /**
     * Request object
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSessionMock;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->resultRedirectMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\Response\RedirectInterface',
            [],
            '',
            false
        );
        $this->requestMock = $this->getMockBuilder('Magento\Framework\App\RequestInterface')
            ->setMethods(['getControllerName', 'getPostValue'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->backendSessionMock = $this->getMock(
            '\Magento\Backend\Model\Session',
            ['setData'],
            [],
            '',
            false
        );
        $this->model = $this->objectManager->getObject(
            '\Magento\AdminGws\Model\Plugin\SaveRole',
            [
                'resultRedirect' => $this->resultRedirectMock,
                'request' => $this->requestMock,
                'backendSession' => $this->backendSessionMock,
            ]
        );
    }

    public function testAfterExecute()
    {
        $redirectUrl = 'http://website.com/admin/admin/user_role/editrole/id/2';
        $saveRoleController = $this->getMock(
            '\Magento\User\Controller\Adminhtml\User\Role\SaveRole',
            [],
            [],
            '',
            false
        );
        $result = $this->getMock(
            '\Magento\Backend\Model\View\Result\Redirect',
            [],
            [],
            '',
            false
        );
        $this->requestMock->expects($this->once())
            ->method('getControllerName')
            ->willReturn('user_role');
        $this->resultRedirectMock->expects($this->once())
            ->method('getRedirectUrl')
            ->willReturn($redirectUrl);
        $this->requestMock->expects($this->once())
            ->method('getPostValue');

        $this->assertEquals(
            $result,
            $this->model->afterExecute($saveRoleController, $result)
        );
    }
}
