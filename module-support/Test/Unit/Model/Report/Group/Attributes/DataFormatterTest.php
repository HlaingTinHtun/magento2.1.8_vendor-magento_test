<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Group\Attributes;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class DataFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Support\Model\Report\Group\Attributes\DataFormatter
     */
    protected $dataFormatter;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->dataFormatter = $this->objectManagerHelper->getObject(
            'Magento\Support\Model\Report\Group\Attributes\DataFormatter'
        );
    }

    /**
     * @param string|null $className
     * @param string $expectedResult
     *
     * @dataProvider prepareModelValueDataProvider
     */
    public function testPrepareModelValue($className, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->dataFormatter->prepareModelValue($className));
    }

    /**
     * @return array
     */
    public function prepareModelValueDataProvider()
    {
        return [
            ['className' => null, 'expectedResult' => ''],
            [
                'className' => 'Some\Model\Class\Name',
                'expectedResult' => 'Some\Model\Class\Name' . "\n" . '{Some/Model/Class/Name.php}'
            ]
        ];
    }
}
