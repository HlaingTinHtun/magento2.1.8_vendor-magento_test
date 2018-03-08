<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedSalesRule\Test\Unit\Model\ResourceModel\Rule\Condition;

use Magento\AdvancedRule\Model\Condition\Filter as FilterModel;

/**
 * Class FilterTest
 */
class FilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedSalesRule\Model\ResourceModel\Rule\Condition\Filter
     */
    protected $model;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\DB\Select
     */
    protected $selectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\ResourceConnection
     */
    protected $resourceMock;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->connectionMock = $this->getMockBuilder('Magento\Framework\DB\Adapter\Pdo\Mysql')
            ->disableOriginalConstructor()
            ->getMock();
        $selectRendererMock = $this->getMockBuilder('\Magento\Framework\DB\Select\SelectRenderer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->selectMock = $this->getMock(
            '\Magento\Framework\DB\Select',
            null,
            [
                $this->connectionMock,
                $selectRendererMock,
            ]
        );

        $this->connectionMock->expects($this->any())
            ->method('select')
            ->willReturn($this->selectMock);
        $this->connectionMock
            ->expects($this->any())
            ->method('quoteInto')
            ->willReturnCallback(
                function ($value) {
                    return "'$value'";
                }
            );

        $contextMock = $this->getMockBuilder('\Magento\Framework\Model\ResourceModel\Db\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resourceMock = $this->getMockBuilder('\Magento\Framework\App\ResourceConnection')
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects($this->any())
            ->method('getResources')
            ->willReturn($this->resourceMock);

        $this->model = $this->objectManager->getObject(
            'Magento\AdvancedSalesRule\Model\ResourceModel\Rule\Condition\Filter',
            [
                'context' => $contextMock,
            ]
        );
    }

    public function testGetFilterTextGenerators()
    {
        $result = ['dummy'];

        $this->setupResource();

        $this->connectionMock->expects($this->once())
            ->method('fetchAll')
            ->with($this->selectMock)
            ->willReturn($result);

        $this->assertEquals($result, $this->model->getFilterTextGenerators());
        $groupParts = $this->selectMock->getPart(\Magento\Framework\DB\Select::GROUP);
        $fromParts = $this->selectMock->getPart(\Magento\Framework\DB\Select::FROM);

        $this->assertEquals(FilterModel::KEY_FILTER_TEXT_GENERATOR_CLASS, $groupParts[0]);
        $this->assertEquals(FilterModel::KEY_FILTER_TEXT_GENERATOR_ARGUMENTS, $groupParts[1]);
        $this->assertEquals('magento_salesrule_filter', array_keys($fromParts)[0]);
    }

    public function testFilterRules()
    {
        $result = ['1' => '1', '3' => '3'];
        $filterText = ["product:category:4", "true"];

        $this->setupResource();

        $this->connectionMock->expects($this->once())
            ->method('fetchAssoc')
            ->with($this->selectMock)
            ->willReturn($result);

        $this->assertEquals(array_keys($result), $this->model->filterRules($filterText));
        $groupParts = $this->selectMock->getPart(\Magento\Framework\DB\Select::GROUP);
        $fromParts = $this->selectMock->getPart(\Magento\Framework\DB\Select::FROM);
        $havingParts = $this->selectMock->getPart(\Magento\Framework\DB\Select::HAVING);

        $this->assertEquals('group_id', $groupParts[0]);
        $this->assertEquals('rule_id', $groupParts[1]);
        $this->assertEquals("(sum(weight) > 0.999)", $havingParts[0]);
        $this->assertEquals('magento_salesrule_filter', array_keys($fromParts)[0]);
    }

    public function testDeleteRuleFilters()
    {
        $ruleIdArray = ['1', '2'];

        $this->setupResource();

        $this->connectionMock->expects($this->once())
            ->method('delete')
            ->with(
                'magento_salesrule_filter',
                ['rule_id IN (?)' => $ruleIdArray]
            );
        $this->assertTrue($this->model->deleteRuleFilters($ruleIdArray));
    }

    public function testDeleteRuleFiltersNoRuleId()
    {
        $this->assertFalse($this->model->deleteRuleFilters(null));
    }

    public function testInsertFilters()
    {
        $data = [
            'rule_id' => 1,
            'group_id' => 1,
            'weight' => 1,
            'filter_text' => 'product:category:4',
            'filter_text_generator_class' =>
                'Magento\AdvancedSalesRule\Model\Rule\Condition\FilterTextGenerator\Product\Category',
            'filter_text_generator_arguments' => [],
        ];
        $this->setupResource();

        $this->connectionMock->expects($this->once())
            ->method('insertMultiple')
            ->with(
                'magento_salesrule_filter',
                $data
            );
        $this->assertTrue($this->model->insertFilters($data));
    }

    public function testInsertFiltersNonArray()
    {
        $this->assertFalse($this->model->insertFilters(null));
    }

    private function setupResource()
    {
        $this->resourceMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->connectionMock);
        $this->resourceMock->expects($this->once())
            ->method('getTableName')
            ->with('magento_salesrule_filter', 'default')
            ->willReturn('magento_salesrule_filter');
    }
}
