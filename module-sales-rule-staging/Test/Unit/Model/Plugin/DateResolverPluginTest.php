<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRuleStaging\Test\Unit\Model\Plugin;

use Magento\SalesRuleStaging\Model\Plugin\DateResolverPlugin;
use Magento\Staging\Api\Data\UpdateInterface;
use Magento\Staging\Api\UpdateRepositoryInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Tests \Magento\SalesRuleStaging\Model\Plugin\DateResolverPlugin.
 */
class DateResolverPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateResolverPlugin
     */
    private $plugin;

    /**
     * @var UpdateRepositoryInterface|MockObject
     */
    private $updateRepository;

    /**
     * @var UpdateInterface|MockObject
     */
    private $update;

    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    private $subject;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->updateRepository = $this->getMockForAbstractClass(UpdateRepositoryInterface::class);
        $this->update = $this->getMockForAbstractClass(UpdateInterface::class);

        $this->prepareObjectManager([
            [
                \Magento\Framework\Api\ExtensionAttributesFactory::class,
                $this->getMock(\Magento\Framework\Api\ExtensionAttributesFactory::class, [], [], '', false)
            ],
            [
                \Magento\Framework\Api\AttributeValueFactory::class,
                $this->getMock(\Magento\Framework\Api\AttributeValueFactory::class, [], [], '', false)
            ],
        ]);

        $this->subject = $objectManager->getObject(\Magento\SalesRule\Model\Rule::class);

        $this->plugin = new DateResolverPlugin($this->updateRepository);
    }

    /**
     * @covers \Magento\SalesRuleStaging\Model\Plugin\DateResolverPlugin::beforeGetFromDate
     */
    public function testBeforeGetFromDate()
    {
        $startDate = date('Y-m-d', strtotime('+1 day'));
        $fromDate = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->subject->setData('from_date', $fromDate);

        $this->updateRepository->expects(static::atLeastOnce())
            ->method('get')
            ->willReturn($this->update);

        $this->update->expects(static::atLeastOnce())
            ->method('getStartTime')
            ->willReturn($startDate);

        $this->plugin->beforeGetFromDate($this->subject);
        static::assertEquals($startDate, $this->subject->getFromDate());
    }

    /**
     * Tests beforeGetFromDate() with the same date, but another time.
     *
     * @covers \Magento\SalesRuleStaging\Model\Plugin\DateResolverPlugin::beforeGetFromDate
     */
    public function testBeforeGetFromDateWithoutUpdate()
    {
        /** @var string $fromDate */
        $fromDate = date('Y-m-d', strtotime('+1 hour'));
        $this->subject->setData('from_date', $fromDate);
        $this->updateRepository->expects($this->atLeastOnce())
            ->method('get')
            ->willReturn($this->update);
        $this->update->expects($this->atLeastOnce())
            ->method('getStartTime')
            ->willReturn(null);

        $this->plugin->beforeGetFromDate($this->subject);
        $this->assertEquals($fromDate, $this->subject->getFromDate());
    }

    /**
     * @covers \Magento\SalesRuleStaging\Model\Plugin\DateResolverPlugin::beforeGetToDate
     */
    public function testBeforeGetToDate()
    {
        $endDate = date('Y-m-d', strtotime('+2 day'));
        $toDate = date('Y-m-d H:i:s', strtotime('+1 day'));

        $this->subject->setData('to_date', $toDate);

        $this->updateRepository->expects(static::atLeastOnce())
            ->method('get')
            ->willReturn($this->update);

        $this->update->expects(static::atLeastOnce())
            ->method('getEndTime')
            ->willReturn($endDate);

        $this->plugin->beforeGetToDate($this->subject);
        static::assertEquals($endDate, $this->subject->getToDate());
    }

    /**
     * Tests beforeGetToDate() with the same date, but another time.
     *
     * @covers \Magento\SalesRuleStaging\Model\Plugin\DateResolverPlugin::beforeGetToDate
     */
    public function testBeforeGetToDateWithoutUpdate()
    {
        /** @var \DateTime $toDate */
        $toDate = new \DateTime(date('Y-m-d H:i:s', strtotime('+1 day')));
        $this->subject->setData('to_date', $toDate);
        $this->updateRepository->expects($this->atLeastOnce())
            ->method('get')
            ->willReturn($this->update);
        $this->update->expects($this->atLeastOnce())
            ->method('getEndTime')
            ->willReturn(null);

        $this->plugin->beforeGetToDate($this->subject);
        $this->assertEquals($toDate->format('Y-m-d'), $this->subject->getToDate());
    }

    /**
     * @param $map
     */
    private function prepareObjectManager($map)
    {
        $objectManagerMock = $this->getMock(\Magento\Framework\ObjectManagerInterface::class);
        $objectManagerMock->expects($this->any())->method('getInstance')->willReturnSelf();
        $objectManagerMock->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($map));
        $reflectionClass = new \ReflectionClass(\Magento\Framework\App\ObjectManager::class);
        $reflectionProperty = $reflectionClass->getProperty('_instance');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($objectManagerMock);
    }
}
