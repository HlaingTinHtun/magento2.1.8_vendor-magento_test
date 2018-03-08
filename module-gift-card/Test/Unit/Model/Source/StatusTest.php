<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCard\Test\Unit\Model\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCard\Model\Source\Status
     */
    protected $model;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\GiftCard\Model\Source\Status');
    }

    public function testToOptionArray()
    {
        $expected = [
            [
                'value' => '1',
                'label' => 'Ordered',
            ],
            [
                'value' => '9',
                'label' => 'Invoiced'
            ],

        ];

        $this->assertEquals($expected, $this->model->toOptionArray());
    }
}
