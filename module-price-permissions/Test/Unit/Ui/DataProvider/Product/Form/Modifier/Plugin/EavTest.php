<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PricePermissions\Test\Unit\Ui\DataProvider\Product\Form\Modifier\Plugin;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\PricePermissions\Observer\ObserverData;
use Magento\PricePermissions\Ui\DataProvider\Product\Form\Modifier\Plugin\Eav;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Eav as EavModifier;

/**
 * Class EavTest
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EavTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Eav
     */
    protected $model;

    /**
     * @var ObserverData|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerDataMock;

    /**
     * @var ArrayManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $arrayManagerMock;

    /**
     * @var LocatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $locatorMock;

    /**
     * @var ProductAttributeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeMock;

    /**
     * @var EavModifier|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->observerDataMock = $this->getMockBuilder(ObserverData::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->arrayManagerMock = $this->getMockBuilder(ArrayManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->locatorMock = $this->getMockBuilder(LocatorInterface::class)
            ->getMockForAbstractClass();
        $this->attributeMock = $this->getMockBuilder(ProductAttributeInterface::class)
            ->getMockForAbstractClass();
        $this->subjectMock = $this->getMockBuilder(EavModifier::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->arrayManagerMock->expects($this->any())
            ->method('merge')
            ->willReturnArgument(1);

        $this->model = $objectManager->getObject(Eav::class, [
            'observerData' => $this->observerDataMock,
            'arrayManager' => $this->arrayManagerMock,
            'locator' => $this->locatorMock,
        ]);
    }

    public function testAroundSetupAttributeMeta()
    {
        $this->attributeMock->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn(ProductAttributeInterface::CODE_STATUS);

        $result = ['code' => 'value'];
        $proceed = function () use ($result) {
            return $result;
        };

        $this->assertSame(
            $result,
            $this->model->aroundSetupAttributeMeta(
                $this->subjectMock,
                $proceed,
                $this->attributeMock,
                'test_group_code',
                1
            )
        );
    }
}
