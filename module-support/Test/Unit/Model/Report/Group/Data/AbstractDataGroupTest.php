<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Group\Data;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

abstract class AbstractDataGroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|MockObject
     */
    protected $connectionMock;

    /**
     * @var \Magento\Framework\Module\ModuleResource|MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Eav\Model\Config|MockObject
     */
    protected $eavConfigMock;

    /**
     * @var \Psr\Log\LoggerInterface|MockObject
     */
    protected $loggerMock;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var string
     */
    protected $reportNamespace = '';

    /**
     * @var \Magento\Support\Model\Report\Group\Data\AbstractDataGroup
     */
    protected $report;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->connectionMock = $this->getMock('Magento\Framework\DB\Adapter\AdapterInterface');
        $this->eavConfigMock = $this->getMock('Magento\Eav\Model\Config', [], [], '', false);
        $this->loggerMock = $this->getMock('Psr\Log\LoggerInterface');

        /** @var \Magento\Framework\Module\ModuleResource|MockObject $resourceMock */
        $this->resourceMock = $this->getMock('\Magento\Framework\Module\ModuleResource', [], [], '', false);
        $this->resourceMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->connectionMock);

        /** @var \Magento\Eav\Model\ConfigFactory|MockObject $eavConfigFactoryMock */
        $eavConfigFactoryMock = $this->getMock('Magento\Eav\Model\ConfigFactory', ['create'], [], '', false);
        $eavConfigFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->eavConfigMock);

        $this->report = $this->objectManagerHelper->getObject(
            $this->reportNamespace,
            [
                'logger' => $this->loggerMock,
                'resource' => $this->resourceMock,
                'eavConfigFactory' => $eavConfigFactoryMock
            ]
        );
    }

    /**
     * @return void
     */
    public function testGenerateWithException()
    {
        $expectedResult = $this->getExpectedResult();
        $e = new \Exception();
        $this->resourceMock->expects($this->once())
            ->method('getTable')
            ->willThrowException($e);
        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($e);

        $this->assertEquals($expectedResult, $this->report->generate());
    }

    /**
     * @param string $entityType
     * @param int $entityTypeId
     */
    protected function entityTypeTest($entityType, $entityTypeId)
    {
        /** @var \Magento\Eav\Model\Entity\Type|MockObject $typeMock */
        $typeMock = $this->getMock('Magento\Eav\Model\Entity\Type', [], [], '', false);
        $typeMock->expects($this->once())
            ->method('getId')
            ->willReturn($entityTypeId);
        $this->eavConfigMock->expects($this->once())
            ->method('getEntityType')
            ->with($entityType)
            ->willReturn($typeMock);
    }

    /**
     * @param string $attributeCode
     * @param string $eavTable
     * @param int $entityTypeId
     * @return string
     */
    protected function getSqlAttributeId($attributeCode, $eavTable, $entityTypeId)
    {
        return 'SELECT `attribute_id`'
        . ' FROM `' . $eavTable . '`'
        . ' WHERE `attribute_code` = "' . $attributeCode . '" AND `entity_type_id` = ' . $entityTypeId;
    }
}
