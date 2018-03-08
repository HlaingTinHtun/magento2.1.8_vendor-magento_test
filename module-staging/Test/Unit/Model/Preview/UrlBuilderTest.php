<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Model\Preview;

use Magento\Staging\Model\Preview\UrlBuilder;
use Magento\Staging\Model\VersionManager;

class UrlBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $coreUrlBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $frontendUrlMock;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    protected function setUp()
    {
        $this->coreUrlBuilderMock = $this->getMock('Magento\Framework\UrlInterface');
        $this->frontendUrlMock = $this->getMock('Magento\Framework\Url', [], [], '', false);
        $this->urlBuilder = new UrlBuilder(
            $this->coreUrlBuilderMock,
            $this->frontendUrlMock
        );
    }

    public function testGetPreviewUrl()
    {
        $baseUrl = 'http://www.example.com';
        $versionId = 1;
        $this->coreUrlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with(
                UrlBuilder::URL_PATH_PREVIEW,
                [
                    '_query' => [
                        UrlBuilder::PARAM_PREVIEW_VERSION => $versionId,
                        UrlBuilder::PARAM_PREVIEW_URL => $baseUrl
                    ]
                ]
            );
        $this->urlBuilder->getPreviewUrl($versionId, $baseUrl);
    }

    public function testGetFrontendPreviewUrl()
    {
        $baseUrl = 'http://www.example.com';
        $versionId = 1;
        $this->frontendUrlMock->expects($this->once())->method('getUrl')->willReturn($baseUrl);
        $this->coreUrlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with(
                UrlBuilder::URL_PATH_PREVIEW,
                [
                    '_query' => [
                        UrlBuilder::PARAM_PREVIEW_VERSION => $versionId,
                        UrlBuilder::PARAM_PREVIEW_URL => $baseUrl
                    ],
                ]
            );
        $this->urlBuilder->getPreviewUrl($versionId);
    }
}
