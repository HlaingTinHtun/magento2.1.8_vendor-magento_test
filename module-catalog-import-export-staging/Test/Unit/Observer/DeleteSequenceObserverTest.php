<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogImportExportStaging\Test\Unit\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class DeleteSequenceObserverTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $objectManager = new ObjectManager($this);
        $productSequenceCollectionMock = $this->getMock(
            \Magento\CatalogStaging\Model\ResourceModel\ProductSequence\Collection::class,
            [],
            [],
            '',
            false
        );
        $observerMock = $this->getMock(\Magento\Framework\Event\Observer::class, ['getIdsToDelete'], [], '', false);
        /** @var \Magento\CatalogImportExportStaging\Observer\DeleteSequenceObserver $model */
        $model = $objectManager->getObject(
            \Magento\CatalogImportExportStaging\Observer\DeleteSequenceObserver::class,
            [
                'productSequenceCollection' => $productSequenceCollectionMock
            ]
        );
        $ids = [1, 2, 3];
        $observerMock->method('getIdsToDelete')
            ->willReturn($ids);
        $productSequenceCollectionMock->expects($this->once())
            ->method('deleteSequence')
            ->with($ids);
        $model->execute($observerMock);
    }
}
