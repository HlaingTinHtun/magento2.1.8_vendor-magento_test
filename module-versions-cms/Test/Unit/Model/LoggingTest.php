<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VersionsCms\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class LoggingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\VersionsCms\Model\Logging
     */
    protected $logging;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestInterface;

    /**
     * @var \Magento\Logging\Model\Event|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventModel;

    protected function setUp()
    {
        $this->requestInterface = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->eventModel = $this->getMockBuilder('\Magento\Logging\Model\Event')
            ->setMethods(['setInfo', '__wakeup', '__sleep'])
            ->disableOriginalConstructor()->getMock();
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->logging = $this->objectManagerHelper->getObject(
            'Magento\VersionsCms\Model\Logging',
            [
                'request' => $this->requestInterface
            ]
        );
    }

    public function testPostDispatchCmsHierachyView()
    {
        $this->eventModel->expects($this->once())->method('setInfo')->with('Tree Viewed')->will($this->returnSelf());
        $this->logging->postDispatchCmsHierachyView([], $this->eventModel);
    }
}
