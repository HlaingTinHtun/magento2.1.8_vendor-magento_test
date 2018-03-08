<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedSalesRule\Test\Unit\Model\Indexer;

use Magento\AdvancedSalesRule\Model\Indexer\SalesRule;

/**
 * Class SalesRuleTest
 */
class SalesRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedSalesRule\Model\Indexer\SalesRule
     */
    protected $model;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fullActionFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rowsActionFactory;

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

        $className = '\Magento\AdvancedSalesRule\Model\Indexer\SalesRule\Action\FullFactory';
        /** @var \Magento\AdvancedSalesRule\Model\Indexer\SalesRule\Action\FullFactory fullActionFactory */
        $this->fullActionFactory = $this->getMock($className, ['create'], [], '', false);

        $className = '\Magento\AdvancedSalesRule\Model\Indexer\SalesRule\Action\RowsFactory';
        $this->rowsActionFactory = $this->getMock($className, ['create'], [], '', false);

        $this->model = $this->objectManager->getObject(
            'Magento\AdvancedSalesRule\Model\Indexer\SalesRule',
            [
                'fullActionFactory' => $this->fullActionFactory,
                'rowsActionFactory' => $this->rowsActionFactory,
            ]
        );
    }

    /**
     * test Execute
     */
    public function testExecute()
    {
        $ids = [1, 2, 3];

        $className = '\Magento\AdvancedSalesRule\Model\Indexer\SalesRule\Action\Rows';
        $rowsAction = $this->getMock($className, [], [], '', false);

        $this->rowsActionFactory->expects($this->any())
            ->method('create')
            ->willReturn($rowsAction);

        $rowsAction->expects($this->once())
            ->method('execute')
            ->with($ids);

        $this->model->execute($ids);
    }

    /**
     * test ExecuteFull
     */
    public function testExecuteFull()
    {
        $className = '\Magento\AdvancedSalesRule\Model\Indexer\SalesRule\Action\Full';
        $fullAction = $this->getMock($className, [], [], '', false);

        $this->fullActionFactory->expects($this->any())
            ->method('create')
            ->willReturn($fullAction);

        $fullAction->expects($this->once())
            ->method('execute')
            ->willReturnSelf();

        $this->model->executeFull();
    }

    /**
     * test ExecuteList
     */
    public function testExecuteList()
    {
        $ids = [1, 2, 3];

        $className = '\Magento\AdvancedSalesRule\Model\Indexer\SalesRule\Action\Rows';
        $rowsAction = $this->getMock($className, [], [], '', false);

        $this->rowsActionFactory->expects($this->any())
            ->method('create')
            ->willReturn($rowsAction);

        $rowsAction->expects($this->once())
            ->method('execute')
            ->with($ids);

        $this->model->executeList($ids);
    }

    /**
     * test ExecuteRow
     */
    public function testExecuteRow()
    {
        $id = 1;
        $ids = [$id];

        $className = '\Magento\AdvancedSalesRule\Model\Indexer\SalesRule\Action\Rows';
        $rowsAction = $this->getMock($className, [], [], '', false);

        $this->rowsActionFactory->expects($this->any())
            ->method('create')
            ->willReturn($rowsAction);

        $rowsAction->expects($this->once())
            ->method('execute')
            ->with($ids);

        $this->model->executeRow($id);
    }
}
