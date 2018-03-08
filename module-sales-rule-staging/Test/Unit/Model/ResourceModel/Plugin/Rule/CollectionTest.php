<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRuleStaging\Test\Unit\Model\ResourceModel\Plugin\Rule;

/**
 * Class CollectionTest
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRuleStaging\Model\ResourceModel\Plugin\Rule\Collection
     */
    protected $plugin;

    /**
     * @var \Magento\Staging\Model\VersionManager\Proxy|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $versionManagerMock;

    /**
     * @var \Magento\Quote\Model\Quote\Address
     */
    protected $address;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleCollectionMock;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $className = '\Magento\Staging\Model\VersionManager\Proxy';
        $this->versionManagerMock = $this->getMock($className, ['isPreviewVersion'], [], '', false);

        $className = '\Magento\Quote\Model\Quote\Address';
        $this->address = $objectManager->getObject($className);

        $className = '\Magento\SalesRule\Model\ResourceModel\Rule\Collection';
        $this->ruleCollectionMock = $this->getMock($className, [], [], '', false);

        $className = '\Magento\SalesRuleStaging\Model\ResourceModel\Plugin\Rule\Collection';
        $this->plugin = $objectManager->getObject(
            $className,
            [
                'versionManager' => $this->versionManagerMock
            ]
        );
    }

    /**
     * Test the beforeSetValidationFilter(...) method
     *
     * @param bool $isPreviewVersion
     * @param bool $expectedToSkipValidationFilter
     * @dataProvider beforeSetValidationFilterDataProvider
     */
    public function testBeforeSetValidationFilter($isPreviewVersion, $expectedToSkipValidationFilter)
    {
        // flesh out versionManager
        $this->versionManagerMock->expects($this->any())
            ->method('isPreviewVersion')
            ->willReturn($isPreviewVersion);

        // before: ensure that output flag is either false or null
        $skipValFilter = $this->address->getData('skip_validation_filter');
        $this->assertTrue(!$skipValFilter, "Expected 'skip_validation_filter' flag to be false or null");

        // invoke the plugin
        $this->plugin->beforeSetValidationFilter($this->ruleCollectionMock, 1, 1, '', null, $this->address);

        // after: ensure the output flag is the expected result
        $skipValFilter = $this->address->getData('skip_validation_filter');
        $this->assertEquals($expectedToSkipValidationFilter, $skipValFilter);
    }

    /**
     * @return array
     */
    public function beforeSetValidationFilterDataProvider()
    {
        return [
            'preview_mode' => [true, true],
            'present_mode' => [false, false],
        ];
    }

    /**
     * Test the beforeSetValidationFilter(...) method when there is no address
     */
    public function testBeforeSetValidationFilterNoAddress()
    {
        // ensure no blowouts when invoked without an address
        $address = null;
        $this->plugin->beforeSetValidationFilter($this->ruleCollectionMock, 1, 1, '', null, $address);
    }
}
