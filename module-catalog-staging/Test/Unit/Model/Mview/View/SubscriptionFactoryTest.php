<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogStaging\Test\Unit\Model\Mview\View;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\CatalogStaging\Model\Mview\View\SubscriptionFactory;
use Magento\Framework\Mview\View\SubscriptionFactory as FrameworkSubstrictionFactory;

class SubscriptionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $versionTablesrMock;

    /**
     * @var \Magento\CatalogStaging\Model\Mview\View\SubscriptionFactory
     */
    protected $model;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->versionTablesrMock = $this->getMockBuilder('Magento\CatalogStaging\Model\VersionTables')
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = $objectManager->getObject(
            SubscriptionFactory::class,
            [
                'objectManager' => $this->objectManagerMock,
                'versionTables' => $this->versionTablesrMock
            ]
        );
    }

    public function testCreate()
    {
        $data = ['tableName' => 'catalog_product_entity_int', 'columnName' => 'entity_id'];
        $versionTables = ['catalog_product_entity_int'];

        $expectedData = $data;
        $expectedData['columnName'] = 'row_id';

        $this->versionTablesrMock->expects($this->once())
            ->method('getVersionTables')
            ->willReturn($versionTables);
        $subscriptionMock = $this->getMockBuilder('Magento\Framework\Mview\View\SubscriptionInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(FrameworkSubstrictionFactory::INSTANCE_NAME, $expectedData)
            ->willReturn($subscriptionMock);

        $result = $this->model->create($data);
        $this->assertEquals($subscriptionMock, $result);
    }

    public function testCreateNoTableName()
    {
        $data = ['columnName' => 'entity_id'];

        $expectedData = $data;

        $subscriptionMock = $this->getMockBuilder('Magento\Framework\Mview\View\SubscriptionInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(FrameworkSubstrictionFactory::INSTANCE_NAME, $expectedData)
            ->willReturn($subscriptionMock);

        $result = $this->model->create($data);
        $this->assertEquals($subscriptionMock, $result);
    }

    /**
     * @param $stagingEntityTable
     * @dataProvider tablesDataProvider
     */
    public function testCreateStagingEntityTables($stagingEntityTable)
    {
        $data = ['tableName' => $stagingEntityTable, 'columnName' => 'entity_id'];

        $expectedData = $data;
        $subscriptionMock = $this->getMockBuilder('Magento\Framework\Mview\View\SubscriptionInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(FrameworkSubstrictionFactory::INSTANCE_NAME, $expectedData)
            ->willReturn($subscriptionMock);

        $result = $this->model->create($data);
        $this->assertEquals($subscriptionMock, $result);
    }

    public static function tablesDataProvider()
    {
        return [
            ['catalog_product_entity'],
            ['catalog_category_entity']
        ];
    }

    public function testCreateNoVersionTable()
    {
        $data = ['tableName' => 'not_existed_table', 'columnName' => 'entity_id'];
        $versionTables = ['catalog_product_entity_int'];

        $expectedData = $data;

        $this->versionTablesrMock->expects($this->once())
            ->method('getVersionTables')
            ->willReturn($versionTables);
        $subscriptionMock = $this->getMockBuilder('Magento\Framework\Mview\View\SubscriptionInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(FrameworkSubstrictionFactory::INSTANCE_NAME, $expectedData)
            ->willReturn($subscriptionMock);

        $result = $this->model->create($data);
        $this->assertEquals($subscriptionMock, $result);
    }
}
