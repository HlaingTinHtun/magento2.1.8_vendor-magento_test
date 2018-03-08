<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CmsStaging\Test\Unit\Block\Adminhtml\Page\Update;

use Magento\CmsStaging\Block\Adminhtml\Page\Update\Provider;
use Magento\Framework\Exception\NoSuchEntityException;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $pageRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $versionManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $urlProviderMock;

    /**
     * @var Provider
     */
    private $provider;

    protected function setUp()
    {
        $this->pageRepositoryMock = $this->getMock('Magento\Cms\Api\PageRepositoryInterface');
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->urlProviderMock = $this->getMock(
            'Magento\Staging\Ui\Component\Listing\Column\Entity\UrlProviderInterface'
        );

        $this->versionManagerMock = $this->getMock('Magento\Staging\Model\VersionManager', [], [], '', false);

        $this->provider = new Provider(
            $this->requestMock,
            $this->pageRepositoryMock,
            $this->versionManagerMock,
            $this->urlProviderMock
        );
    }

    public function testGetIdReturnsPageIdIfPageExists()
    {
        $pageId = 1;

        $pageMock = $this->getMock('Magento\Cms\Api\Data\PageInterface');
        $pageMock->expects($this->any())->method('getId')->willReturn($pageId);

        $this->requestMock->expects($this->any())->method('getParam')->with('page_id')->willReturn($pageId);
        $this->pageRepositoryMock->expects($this->any())->method('getById')->with($pageId)->willReturn($pageMock);

        $this->assertEquals($pageId, $this->provider->getId());
    }

    public function testGetIdReturnsNullIfPageDoesNotExist()
    {
        $pageId = 9999;

        $this->requestMock->expects($this->any())->method('getParam')->with('page_id')->willReturn($pageId);
        $this->pageRepositoryMock->expects($this->any())
            ->method('getById')
            ->with($pageId)
            ->willThrowException(NoSuchEntityException::singleField('page_id', $pageId));

        $this->assertNull($this->provider->getId());
    }

    public function testGetUrlReturnsUrlBasedOnPageDataIfPageExists()
    {
        $expectedResult = 'http://www.example.com';
        $currentVersionId = 1;
        $updateMock = $this->getMock('Magento\Staging\Api\Data\UpdateInterface');
        $updateMock->expects($this->any())->method('getId')->willReturn($currentVersionId);
        $this->versionManagerMock->expects($this->any())->method('getCurrentVersion')->willReturn($updateMock);

        $pageId = 1;
        $pageData = [
            'id' => $pageId,
        ];
        $pageMock = $this->getMock('Magento\Cms\Model\Page', [], [], '', false);
        $pageMock->expects($this->any())->method('getId')->willReturn($pageId);
        $pageMock->expects($this->any())->method('getData')->willReturn($pageData);

        $this->requestMock->expects($this->any())->method('getParam')->with('page_id')->willReturn($pageId);
        $this->pageRepositoryMock->expects($this->any())->method('getById')->with($pageId)->willReturn($pageMock);

        $this->urlProviderMock->expects($this->any())->method('getUrl')->with($pageData)->willReturn($expectedResult);

        $this->assertEquals($expectedResult, $this->provider->getUrl(1));
    }

    public function testGetUrlReturnsNullIfPageDoesNotExist()
    {
        $currentVersionId = 1;
        $updateMock = $this->getMock('Magento\Staging\Api\Data\UpdateInterface');
        $updateMock->expects($this->any())->method('getId')->willReturn($currentVersionId);
        $this->versionManagerMock->expects($this->any())->method('getCurrentVersion')->willReturn($updateMock);

        $pageId = 9999;
        $this->requestMock->expects($this->any())->method('getParam')->with('page_id')->willReturn($pageId);
        $this->pageRepositoryMock->expects($this->any())
            ->method('getById')
            ->with($pageId)
            ->willThrowException(NoSuchEntityException::singleField('page_id', $pageId));

        $this->urlProviderMock->expects($this->never())->method('getUrl');

        $this->assertNull($this->provider->getUrl(1));
    }
}
