<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogStaging\Test\Unit\Block\Adminhtml\Update;

use Magento\Framework\Exception\NoSuchEntityException;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogStaging\Block\Adminhtml\Update\Provider
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $versionManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $urlProviderMock;

    protected function setUp()
    {
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->productRepositoryMock = $this->getMock('Magento\Catalog\Api\ProductRepositoryInterface');
        $this->versionManagerMock = $this->getMock('Magento\Staging\Model\VersionManager', [], [], '', false);
        $this->urlProviderMock = $this->getMock(
            'Magento\CatalogStaging\Ui\Component\Listing\Column\Product\UrlProvider',
            [],
            [],
            '',
            false
        );

        $this->model = new \Magento\CatalogStaging\Block\Adminhtml\Update\Provider(
            $this->requestMock,
            $this->productRepositoryMock,
            $this->versionManagerMock,
            $this->urlProviderMock
        );
    }

    public function testGetId()
    {
        $productId = 100;

        $productMock = $this->getMock('Magento\Catalog\Api\Data\ProductInterface');
        $productMock->expects($this->once())->method('getId')->willReturn($productId);

        $this->requestMock->expects($this->once())->method('getParam')->with('id')->willReturn($productId);
        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $this->assertEquals($productId, $this->model->getId());
    }

    public function testGetIdThrowsExceptionIfProductDoesNotExist()
    {
        $productId = 100;

        $this->requestMock->expects($this->once())->method('getParam')->with('id')->willReturn($productId);
        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willThrowException(new \Magento\Framework\Exception\NoSuchEntityException());
        $this->assertNull($this->model->getId());
    }

    public function testGetUrlReturnsUrlBasedOnProductDataIfProductExists()
    {
        $expectedResult = 'http://www.example.com';
        $currentVersionId = 1;
        $updateMock = $this->getMock('Magento\Staging\Api\Data\UpdateInterface');
        $updateMock->expects($this->any())->method('getId')->willReturn($currentVersionId);
        $this->versionManagerMock->expects($this->any())->method('getCurrentVersion')->willReturn($updateMock);

        $productId = 1;
        $productData = [
            'id' => $productId,
        ];
        $productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $productMock->expects($this->any())->method('getId')->willReturn($productId);
        $productMock->expects($this->any())->method('getData')->willReturn($productData);

        $this->requestMock->expects($this->any())->method('getParam')->with('id')->willReturn($productId);
        $this->productRepositoryMock->expects($this->any())
            ->method('getById')
            ->with($productId)
            ->willReturn($productMock);

        $this->urlProviderMock->expects($this->any())
            ->method('getUrl')
            ->with($productData)
            ->willReturn($expectedResult);

        $this->assertEquals($expectedResult, $this->model->getUrl(1));
    }

    public function testGetUrlReturnsNullIfProductDoesNotExist()
    {
        $currentVersionId = 1;
        $updateMock = $this->getMock('Magento\Staging\Api\Data\UpdateInterface');
        $updateMock->expects($this->any())->method('getId')->willReturn($currentVersionId);
        $this->versionManagerMock->expects($this->any())->method('getCurrentVersion')->willReturn($updateMock);

        $productId = 9999;
        $this->requestMock->expects($this->any())->method('getParam')->with('id')->willReturn($productId);
        $this->productRepositoryMock->expects($this->any())
            ->method('getById')
            ->with($productId)
            ->willThrowException(NoSuchEntityException::singleField('id', $productId));

        $this->urlProviderMock->expects($this->never())->method('getUrl');

        $this->assertNull($this->model->getUrl(1));
    }
}
