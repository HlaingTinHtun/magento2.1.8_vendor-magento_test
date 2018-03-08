<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogStaging\Test\Unit\Plugin\Model;

use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\Product;
use Magento\CatalogStaging\Plugin\Model\UpdateProductDateAttributesPlugin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Staging\Api\Data\UpdateInterface;
use Magento\Staging\Model\VersionManager;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class UpdateProductDateAttributesPluginTest
 */
class UpdateProductDateAttributesPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateProductDateAttributesPlugin
     */
    private $plugin;

    /**
     * @var VersionManager|MockObject
     */
    private $versionManager;

    /**
     * @var UpdateInterface|MockObject
     */
    private $update;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var ProductResource|MockObject
     */
    private $productRepository;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->product = $objectManager->getObject(Product::class);
        $this->productRepository = $this->getMock(ProductResource::class, [], [], '', false);

        $this->versionManager = $this->getMockBuilder(VersionManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCurrentVersion'])
            ->getMock();

        $this->update = $this->getMockForAbstractClass(UpdateInterface::class);

        $this->plugin = new UpdateProductDateAttributesPlugin($this->versionManager);
    }

    /**
     * @covers \Magento\CatalogStaging\Plugin\Model\UpdateProductDateAttributesPlugin::beforeSave
     */
    public function testBeforeSave()
    {
        $startTime = strtotime('+1 day');
        $endTime = strtotime('+3 days');

        $this->versionManager->expects(static::once())
            ->method('getCurrentVersion')
            ->willReturn($this->update);

        $this->update->expects(static::atLeastOnce())
            ->method('getStartTime')
            ->willReturn($startTime);

        $this->update->expects(static::once())
            ->method('getEndTime')
            ->willReturn($endTime);
        $this->product->setIsNew(true);

        $this->plugin->beforeSave($this->productRepository, $this->product);

        static::assertEquals($startTime, $this->product->getData('news_from_date'));
        static::assertEquals($startTime, $this->product->getData('special_from_date'));
        static::assertEquals($startTime, $this->product->getData('custom_design_from'));

        static::assertEquals($endTime, $this->product->getData('news_to_date'));
        static::assertEquals($endTime, $this->product->getData('special_to_date'));
        static::assertEquals($endTime, $this->product->getData('custom_design_to'));
    }

    public function testBeforeSaveWithNullDate()
    {
        $startTime = strtotime('+1 day');
        $endTime = strtotime('+3 days');

        $this->versionManager->expects(static::once())
            ->method('getCurrentVersion')
            ->willReturn($this->update);

        $this->update->expects(static::atLeastOnce())
            ->method('getStartTime')
            ->willReturn($startTime);

        $this->update->expects(static::once())
            ->method('getEndTime')
            ->willReturn($endTime);
        $this->product->setIsNew(false);

        $this->plugin->beforeSave($this->productRepository, $this->product);

        static::assertNull($this->product->getData('news_from_date'));
        static::assertEquals($startTime, $this->product->getData('special_from_date'));
        static::assertEquals($startTime, $this->product->getData('custom_design_from'));

        static::assertNull($this->product->getData('news_to_date'));
        static::assertEquals($endTime, $this->product->getData('special_to_date'));
        static::assertEquals($endTime, $this->product->getData('custom_design_to'));
    }
}
