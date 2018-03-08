<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  unit_tests
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model\Indexer\TargetRule\Plugin;

class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Plugin\Import
     */
    protected $_model;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleProductMock;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productRuleMock;

    protected function setUp()
    {
        $this->_ruleProductMock = $this->getMock(
            \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor::class,
            [],
            [],
            '',
            false
        );
        $this->_productRuleMock = $this->getMock(
            \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor::class,
            [],
            [],
            '',
            false
        );
        $this->_model = new \Magento\TargetRule\Model\Indexer\TargetRule\Plugin\Import(
            $this->_productRuleMock,
            $this->_ruleProductMock
        );
    }

    public function testAfterImportSource()
    {
        $subjectMock = $this->getMock(\Magento\ImportExport\Model\Import::class, [], [], '', false);
        $result = 'result';
        $this->_productRuleMock->expects($this->once())
            ->method('markIndexerAsInvalid');

        $this->_ruleProductMock->expects($this->once())
            ->method('markIndexerAsInvalid');

        $this->assertEquals(
            $result,
            $this->_model->afterImportSource($subjectMock, $result)
        );
    }

    /**
     * @param bool $schedule
     * @dataProvider invalidateIndexersDataProvider
     * @return void
     */
    public function testInvalidateIndexers($schedule)
    {
        $this->_productRuleMock->expects($this->once())
            ->method('isIndexerScheduled')
            ->willReturn($schedule);

        $this->_ruleProductMock->expects($this->once())
            ->method('isIndexerScheduled')
            ->willReturn($schedule);

        if (!$schedule) {
            $this->_productRuleMock->expects($this->once())
                ->method('markIndexerAsInvalid');

            $this->_ruleProductMock->expects($this->once())
                ->method('markIndexerAsInvalid');
        }

        $this->invokeMethod($this->_model, 'invalidateIndexers');
    }

    /**
     * Data provider for test 'testInvalidateIndexers'
     *
     * @return array
     */
    public function invalidateIndexersDataProvider()
    {
        return [
            'Update on save' => [
                '$schedule' => false,
            ],
            'Update by schedule' => [
                '$schedule' => true,
            ]
        ];
    }

    /**
     * @param \Magento\TargetRule\Model\Indexer\TargetRule\Plugin\Import $object
     * @param string $methodName
     * @param array $parameters
     * @return mixed
     */
    protected function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));

        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
