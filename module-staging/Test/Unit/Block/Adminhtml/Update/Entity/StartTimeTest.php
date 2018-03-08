<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Block\Adminhtml\Update\Entity;

class StartTimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $updateRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $versionHistoryMock;

    /**
     * @var \Magento\Staging\Block\Adminhtml\Update\Entity\StartTime
     */
    private $model;

    protected function setUp()
    {
        $processorMock = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\Processor')
            ->disableOriginalConstructor()
            ->getMock();

        $this->contextMock = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\ContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock->expects($this->any())
            ->method('getProcessor')
            ->willReturn($processorMock);

        $uiComponentMock = $this->getMockBuilder('Magento\Framework\View\Element\UiComponentInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $uiComponentMock->expects($this->any())
            ->method('getData')
            ->withAnyParameters()
            ->willReturn(['extends' => ['test']]);

        $uiComponentFactoryMock = $this->getMockBuilder('Magento\Framework\View\Element\UiComponentFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $uiComponentFactoryMock->expects($this->any())
            ->method('create')
            ->withAnyParameters()
            ->willReturn($uiComponentMock);

        $this->updateRepositoryMock = $this->getMockBuilder('Magento\Staging\Api\UpdateRepositoryInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->versionHistoryMock = $this->getMockBuilder('Magento\Staging\Model\VersionHistoryInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject(
            'Magento\Staging\Block\Adminhtml\Update\Entity\StartTime',
            [
                'context' => $this->contextMock,
                'uiComponentFactory' => $uiComponentFactoryMock,
                'updateRepository' => $this->updateRepositoryMock,
                'versionHistory' => $this->versionHistoryMock
            ]
        );
    }

    public function testPrepareActiveCompany()
    {
        $id = 123;
        $data['config']['formElement'] = 'testStartTime';
        $this->model->setData($data);

        $this->contextMock->expects($this->any())
            ->method('getRequestParam')
            ->willReturn($id);
        $dataProvider = 'Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface';
        $dataProviderMock = $this->getMockBuilder($dataProvider)
            ->disableOriginalConstructor()
            ->getMock();
        $dataProviderMock->expects($this->once())
            ->method('getRequestFieldName')
            ->willReturn('id');
        $this->contextMock->expects($this->any())
            ->method('getDataProvider')
            ->willReturn($dataProviderMock);

        $this->versionHistoryMock->expects($this->once())
            ->method('getCurrentId')
            ->willReturn($id);

        $this->model->prepare();
        $data = $this->model->getData();
        $this->assertEquals(1, $data['config']['disabled']);
    }

    public function testPrepareUpcomingCompany()
    {
        $id = 123;
        $data['config']['formElement'] = 'testStartTime';
        $this->model->setData($data);

        $this->contextMock->expects($this->any())
            ->method('getRequestParam')
            ->willReturn($id);
        $dataProvider = 'Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface';
        $dataProviderMock = $this->getMockBuilder($dataProvider)
            ->disableOriginalConstructor()
            ->getMock();
        $dataProviderMock->expects($this->once())
            ->method('getRequestFieldName')
            ->willReturn('id');
        $this->contextMock->expects($this->any())
            ->method('getDataProvider')
            ->willReturn($dataProviderMock);

        $this->versionHistoryMock->expects($this->once())
            ->method('getCurrentId')
            ->willReturn($id + 1);

        $this->model->prepare();
        $data = $this->model->getData();
        $this->assertNotContains('disabled', $data['config']);
    }
}
