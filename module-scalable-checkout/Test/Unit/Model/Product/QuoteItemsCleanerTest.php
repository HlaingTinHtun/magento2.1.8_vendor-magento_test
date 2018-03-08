<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ScalableCheckout\Test\Unit\Model\Product;

class QuoteItemsCleanerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ScalableCheckout\Model\Product\QuoteItemsCleaner
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\MessageQueue\PublisherInterface
     */
    private $publisherMock;

    protected function setUp()
    {
        $this->publisherMock = $this->getMock(\Magento\Framework\MessageQueue\PublisherInterface::class);
        $this->model = new \Magento\ScalableCheckout\Model\Product\QuoteItemsCleaner($this->publisherMock);
    }

    public function testExecute()
    {
        $productMock = $this->getMock(\Magento\Catalog\Api\Data\ProductInterface::class);
        $this->publisherMock->expects($this->once())
            ->method('publish')
            ->with(\Magento\ScalableCheckout\Model\Product\QuoteItemsCleaner::TOPIC_NAME, $productMock);
        $this->model->execute($productMock);
    }
}
