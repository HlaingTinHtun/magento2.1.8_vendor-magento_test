<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model\Actions\Condition\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\TargetRule\Model\Index;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class AttributesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tested model
     *
     * @var \Magento\TargetRule\Model\Actions\Condition\Product\Attributes
     */
    protected $attributes;

    /**
     * Object manager helper
     *
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * Context mock
     *
     * @var \Magento\Rule\Model\Condition\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * Backend helper mock
     *
     * @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendHelperMock;

    /**
     * Config mock
     *
     * @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * Product mock
     *
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * Product resource model mock
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceProductMock;

    /**
     * Attribute set collection mock
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionMock;

    /**
     * Locale format mock
     *
     * @var \Magento\Framework\Locale\FormatInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formatInterfaceMock;

    /**
     * Editable block mock
     *
     * @var \Magento\Rule\Block\Editable|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $editableMock;

    /**
     * Product Type mock
     *
     * @var \Magento\Catalog\Model\Product\Type|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeMock;

    /**
     * Index mock
     *
     * @var Index|\PHPUnit_Framework_MockObject_MockObject
     */
    private $indexMock;

    /**
     * EAV Attribute mock
     *
     * @var Index|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eavAttributeMock;

    /**
     * EAV Attribute mock
     *
     * @var EntityMetadataInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityMetadataMock;

    /**
     * EAV Attribute mock
     *
     * @var MetadataPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataPoolMock;

    /**
     * EAV Attribute mock
     *
     * @var Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $selectMock;

    /**
     * Adapter mock
     *
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $adapterMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMock(\Magento\Rule\Model\Condition\Context::class, [], [], '', false);
        $this->backendHelperMock = $this->getMock(\Magento\Backend\Helper\Data::class, [], [], '', false);
        $this->configMock = $this->getMock(\Magento\Eav\Model\Config::class, [], [], '', false);
        $this->productMock = $this->getMock(\Magento\Catalog\Model\Product::class, [], [], '', false);
        $this->collectionMock = $this->getMock(
            \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection::class,
            [],
            [],
            '',
            false
        );
        $this->formatInterfaceMock = $this->getMock(\Magento\Framework\Locale\FormatInterface::class);
        $this->editableMock = $this->getMock(\Magento\Rule\Block\Editable::class, [], [], '', false);
        $this->typeMock = $this->getMock(\Magento\Catalog\Model\Product\Type::class, [], [], '', false);
        $this->resourceProductMock = $this->getMock(
            \Magento\Catalog\Model\ResourceModel\Product::class,
            [],
            [],
            '',
            false
        );
        $this->resourceProductMock->expects($this->any())->method('loadAllAttributes')->will($this->returnSelf());
        $this->resourceProductMock->expects($this->any())->method('getAttributesByCode')->will($this->returnSelf());
        $this->indexMock = $this->getMockBuilder(Index::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getTable',
                'bindArrayOfIds',
                'getOperatorCondition',
                'getOperatorBindCondition',
                'getResource',
                'select',
                'getConnection'])
            ->getMock();
        $this->eavAttributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->setMethods(['isScopeGlobal', 'isStatic', 'getBackendTable', 'getId'])
            ->getMock();
        $this->selectMock = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->setMethods(['from', 'assemble', 'where', 'joinLeft'])
            ->getMock();
        $this->metadataPoolMock = $this->getMockBuilder(MetadataPool::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMetadata'])
            ->getMock();
        $this->entityMetadataMock = $this->getMockBuilder(EntityMetadataInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLinkField'])
            ->getMockForAbstractClass();
        $this->adapterMock = $this->getMockBuilder(\Magento\Framework\DB\Adapter\AdapterInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getIfNullSql'])
            ->getMockForAbstractClass();
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->attributes = $this->objectManagerHelper->getObject(
            \Magento\TargetRule\Model\Actions\Condition\Product\Attributes::class,
            [
                'context' => $this->contextMock,
                'backendData' => $this->backendHelperMock,
                'config' => $this->configMock,
                'product' => $this->productMock,
                'productResource' => $this->resourceProductMock,
                'attrSetCollection' => $this->collectionMock,
                'localeFormat' => $this->formatInterfaceMock,
                'editable' => $this->editableMock,
                'type' => $this->typeMock
            ]
        );
        $this->objectManagerHelper->setBackwardCompatibleProperty(
            $this->attributes,
            'metadataPool',
            $this->metadataPoolMock
        );
    }

    /**
     * Test get conditions for collection
     *
     * @param string $operator
     * @param string $whereCondition
     * @dataProvider getConditionForCollectionDataProvider
     * @return void
     */
    public function testGetConditionForCollection($operator, $whereCondition)
    {
        $this->attributes->setAttribute('category_ids');
        $this->attributes->setValueType('constant');
        $this->attributes->setValue(3);
        $this->attributes->setOperator($operator);

        $collection = null;

        $this->indexMock->expects($this->any())->method('getTable')->will($this->returnArgument(0));
        $this->indexMock->expects($this->any())->method('bindArrayOfIds')->with(3)->will($this->returnValue([3]));
        $this->indexMock->expects($this->any())->method('getOperatorCondition')
            ->with('category_id', $operator, [3])
            ->will($this->returnValue($whereCondition));

        $object = clone $this->indexMock;

        $this->selectMock->expects($this->any())->method('from')->with('catalog_category_product', 'COUNT(*)')
            ->will($this->returnSelf());
        $this->selectMock->expects($this->at(1))
            ->method('where')
            ->with('product_id=e.entity_id')
            ->will($this->returnSelf());
        $this->selectMock->expects($this->at(2))->method('where')->with($whereCondition)->will($this->returnSelf());
        $this->selectMock->expects($this->any())->method('assemble')->will($this->returnValue('assembled select'));
        $object->expects($this->any())->method('getResource')->will($this->returnValue($this->indexMock));
        $object->expects($this->any())->method('select')->will($this->returnValue($this->selectMock));
        $bind = [];
        $result = $this->attributes->getConditionForCollection($collection, $object, $bind);
        $this->assertEquals(
            '(assembled select) > 0',
            (string)$result
        );
    }

    /**
     * Test Condition For Collection With Global Scope Attribute
     */
    public function testGetConditionForCollectionForGlobalScope()
    {
        $operator = "{}";

        $attribute = 'filter';
        $attributeId = 42;

        $this->attributes->setAttribute($attribute);
        $this->attributes->setValueType('same_as');
        $this->attributes->setValue(3);
        $this->attributes->setOperator($operator);

        $bind[] = [
            "bind" => ":targetrule_bind_0",
            "field" => "filter",
            "callback" => ["bindLikeValue"]
        ];
        $table = 'catalog_product_entity_varchar';
        $linkField = 'row_id';
        $ifNullSql = "IFNULL(relation.parent_id, table.$linkField)";

        $this->configMock->expects($this->once())
            ->method("getAttribute")
            ->with(\Magento\Catalog\Model\Product::ENTITY, $attribute)
            ->willReturn($this->eavAttributeMock);

        $this->eavAttributeMock->expects($this->once())
            ->method('isScopeGlobal')
            ->willReturn(true);
        $this->eavAttributeMock->expects($this->once())
            ->method('isStatic')
            ->willReturn(false);
        $this->eavAttributeMock->expects($this->once())
            ->method('getId')
            ->willReturn($attributeId);
        $this->eavAttributeMock->expects($this->once())
            ->method('getBackendTable')
            ->willReturn('catalog_product_entity_varchar');

        $this->metadataPoolMock->expects($this->once())
            ->method('getMetadata')
            ->with(ProductInterface::class)
            ->willReturn($this->entityMetadataMock);
        $this->entityMetadataMock->expects($this->once())
            ->method('getLinkField')
            ->willReturn($linkField);

        $this->selectMock->expects($this->once())
            ->method('from')
            ->with(
                ['table' => $table],
                $ifNullSql
            )
            ->willReturnSelf();
        $this->selectMock->expects($this->once())
            ->method('joinLeft')
            ->with(
                ['relation' => 'catalog_product_relation'],
                "table.$linkField=relation.child_id",
                []
            )
            ->willReturnSelf();
        $this->selectMock->expects($this->exactly(3))
            ->method('where')
            ->willReturnMap([
                ['table.attribute_id=?', $attributeId, null, $this->selectMock],
                ['table.store_id=?', 0, null, $this->selectMock],
                ['table.value=:targetrule_bind_0', null, null, $this->selectMock],
            ]);

        $this->adapterMock->expects($this->once())
            ->method('getIfNullSql')
            ->with('relation.parent_id', 'table.' . $linkField)
            ->willReturn($ifNullSql);

        $this->indexMock->expects($this->once())
            ->method('getTable')
            ->with('catalog_product_relation')
            ->willReturn('catalog_product_relation');
        $this->indexMock->expects($this->once())
            ->method('getOperatorBindCondition')
            ->with('table.value', $attribute, $operator, $bind)
            ->willReturn('table.value=:targetrule_bind_0');
        $this->indexMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->adapterMock);

        $object = clone $this->indexMock;
        $object->expects($this->once())
            ->method('select')
            ->willReturn($this->selectMock);
        $object->expects($this->once())
            ->method('getResource')
            ->willReturn($this->indexMock);

        $result = $this->attributes->getConditionForCollection(null, $object, $bind);
        $this->assertEquals("e.$linkField IN ()", $result);
    }

    /**
     * Data provider for get conditions for collection test
     *
     * @return array
     */
    public function getConditionForCollectionDataProvider()
    {
        return [
            ['==', "`category_id`='3'"],
            ['()', "`category_id` IN ('3')"],
        ];
    }
}
