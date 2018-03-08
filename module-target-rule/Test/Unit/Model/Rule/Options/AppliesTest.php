<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model\Rule\Options;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class AppliesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tested model
     *
     * @var \Magento\TargetRule\Model\Rule\Options\Applies
     */
    protected $_applies;

    protected function setUp()
    {
        $rule = $this->getMock(
            '\Magento\TargetRule\Model\Rule',
            [],
            [],
            '',
            false
        );

        $rule->expects($this->once())
            ->method('getAppliesToOptions')
            ->will($this->returnValue([1, 2]));

        $this->_applies = (new ObjectManager($this))->getObject(
            '\Magento\TargetRule\Model\Rule\Options\Applies',
            [
                'targetRuleModel' => $rule,
            ]
        );
    }

    public function testToOptionArray()
    {
        $this->assertEquals([1, 2], $this->_applies->toOptionArray());
    }
}
