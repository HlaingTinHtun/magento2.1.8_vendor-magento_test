<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Group\General;

class VersionSectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Support\Model\Report\Group\General\VersionSection
     */
    protected $version;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMetaData;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->productMetaData = $this->getMock('Magento\Framework\App\ProductMetadataInterface');

        $this->version = $this->objectManagerHelper->getObject(
            'Magento\Support\Model\Report\Group\General\VersionSection',
            ['productMetadata' => $this->productMetaData]
        );
    }

    /**
     * @return void
     */
    public function testExecute()
    {
        $edition = 'Enterprise';
        $version = '1.0.0-beta';

        $expectedData = [
            \Magento\Support\Model\Report\Group\General\VersionSection::REPORT_TITLE => [
                'headers' => ['Version'],
                'data' => ['Enterprise 1.0.0-beta']
            ]
        ];

        $this->productMetaData->expects($this->once())->method('getEdition')->willReturn($edition);
        $this->productMetaData->expects($this->once())->method('getVersion')->willReturn($version);

        $this->assertSame($expectedData, $this->version->generate());
    }
}
