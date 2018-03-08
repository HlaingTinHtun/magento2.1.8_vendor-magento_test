<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedSalesRule\Test\Unit\Observer;


/**
 * Class ReindexSalesRuleTest
 */
class ReindexSalesRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedSalesRule\Observer\ReindexSalesRule
     */
    protected $observer;

    /**
     * @var \Magento\AdvancedSalesRule\Model\Indexer\SalesRule\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexProcessorMock;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->indexProcessorMock = $this->getMockBuilder(
            '\Magento\AdvancedSalesRule\Model\Indexer\SalesRule\Processor'
        )->disableOriginalConstructor()
            ->getMock();

        $this->observer = $this->objectManager->getObject(
            'Magento\AdvancedSalesRule\Observer\ReindexSalesRule',
            [
                'indexerProcessor' => $this->indexProcessorMock,
            ]
        );
    }

    /**
     * test Execute
     */
    public function testExecute()
    {
        $ids = [1, 2, 3];

        /** @var \Magento\Framework\Event\Observer $observerData */
        $observerData = $this->objectManager->getObject(
            '\Magento\Framework\Event\Observer',
            [
                'data' => [
                    'entity_ids' => $ids,
                ],
            ]
        );

        $this->indexProcessorMock->expects($this->once())
            ->method('reindexList')
            ->with($ids);

        $this->observer->execute($observerData);
    }
}
