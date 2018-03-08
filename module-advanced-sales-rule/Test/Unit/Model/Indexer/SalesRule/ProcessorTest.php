<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedSalesRule\Test\Unit\Model\Indexer\SalesRule;

use Magento\AdvancedSalesRule\Model\Indexer\SalesRule\Processor;

/**
 * Class ProcessorTest
 */
class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedSalesRule\Model\Indexer\SalesRule\Processor
     */
    protected $model;
    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerRegistry;

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

        $className = '\Magento\Framework\Indexer\IndexerRegistry';
        $this->indexerRegistry = $this->getMock($className, [], [], '', false);

        $this->model = $this->objectManager->getObject(
            'Magento\AdvancedSalesRule\Model\Indexer\SalesRule\Processor',
            [
                'indexerRegistry' => $this->indexerRegistry,
            ]
        );
    }

    /**
     * test GetIndexer
     */
    public function testGetIndexer()
    {
        $className = 'Magento\Indexer\Model';
        $indexer = $this->getMock($className, [], [], '', false);

        $this->indexerRegistry->expects($this->any())
            ->method('get')
            ->willReturn($indexer);
        $this->assertSame($indexer, $this->model->getIndexer());
    }
}
