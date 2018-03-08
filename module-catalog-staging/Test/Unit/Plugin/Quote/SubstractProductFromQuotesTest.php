<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogStaging\Test\Unit\Plugin\Quote;

class SubstractProductFromQuotesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogStaging\Plugin\Quote\SubstractProductFromQuotes
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $versionManagerMock;

    protected function setup()
    {
        $this->versionManagerMock = $this->getMock(\Magento\Staging\Model\VersionManager::class, [], [], '', false);
        $this->model = new \Magento\CatalogStaging\Plugin\Quote\SubstractProductFromQuotes($this->versionManagerMock);
    }

    public function testAroundSubstractProductFromQuotesWhenVersionIsPreview()
    {
        $quoteResourceMock = $this->getMock(\Magento\Quote\Model\ResourceModel\Quote::class, [], [], '', false);
        $productMock = $this->getMock(\Magento\Catalog\Model\Product::class, [], [], '', false);
        $closureResult = 'closure_result';

        $closure = function ($productMock) use ($closureResult) {
            return $closureResult;
        };

        $this->versionManagerMock->expects($this->once())->method('isPreviewVersion')->willReturn(true);

        $this->assertEquals(
            $quoteResourceMock,
            $this->model->aroundSubstractProductFromQuotes($quoteResourceMock, $closure, $productMock)
        );
    }

    public function testAroundSubstractProductFromQuotesWhenVersionIsNotPreview()
    {
        $quoteResourceMock = $this->getMock(\Magento\Quote\Model\ResourceModel\Quote::class, [], [], '', false);
        $productMock = $this->getMock(\Magento\Catalog\Model\Product::class, [], [], '', false);
        $closureResult = 'closure_result';

        $closure = function ($productMock) use ($closureResult) {
            return $closureResult;
        };

        $this->versionManagerMock->expects($this->once())->method('isPreviewVersion')->willReturn(false);

        $this->assertEquals(
            $closureResult,
            $this->model->aroundSubstractProductFromQuotes($quoteResourceMock, $closure, $productMock)
        );
    }
}
