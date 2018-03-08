<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VersionsCms\Test\Unit\Block\Hierarchy;

use Magento\VersionsCms\Model\CurrentNodeResolverInterface;

/**
 * Tests Magento\VersionCms\Block\Hierarchy\MenuTest Class.
 */
class MenuTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\NodeFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $nodeFactoryMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var \Magento\Framework\View\Element\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var CurrentNodeResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $currentNodeResolverMock;

    protected function setUp()
    {
        $this->nodeFactoryMock = $this->getMockBuilder(\Magento\VersionsCms\Model\Hierarchy\NodeFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->getMockForAbstractClass();

        $this->contextMock = $this->getMockBuilder(\Magento\Framework\View\Element\Template\Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->requestMock);

        $this->currentNodeResolverMock = $this->getMockBuilder(CurrentNodeResolverInterface::class)
            ->getMockForAbstractClass();
    }

    /**
     * Test that block menu is brief when menu_brief flag is true.
     */
    public function testIsBriefIsTrue()
    {
        $nodeParams = [
            'menu_visibility' => 1,
            'menu_brief' => 1,
        ];

        $nodeMock = $this->getMockBuilder(\Magento\VersionsCms\Model\Hierarchy\Node::class)
            ->disableOriginalConstructor()
            ->getMock();
        $nodeMock->expects($this->once())
            ->method('getMetadataContextMenuParams')
            ->willReturn($nodeParams);

        $this->currentNodeResolverMock->expects($this->once())
            ->method('get')
            ->with($this->requestMock)
            ->willReturn($nodeMock);

        $blockMock = $this->getBlockMock();

        $this->assertTrue($blockMock->isBrief());
    }

    /**
     * Test that block menu doesn't brief, when menu_brief flag is false.
     */
    public function testIsBriefIsFalse()
    {
        $nodeParams = [
            'menu_visibility' => 1,
            'menu_brief' => 0,
        ];

        $nodeMock = $this->getMockBuilder(\Magento\VersionsCms\Model\Hierarchy\Node::class)
            ->disableOriginalConstructor()
            ->getMock();
        $nodeMock->expects($this->once())
            ->method('getMetadataContextMenuParams')
            ->willReturn($nodeParams);

        $this->currentNodeResolverMock->expects($this->once())
            ->method('get')
            ->with($this->requestMock)
            ->willReturn($nodeMock);

        $blockMock = $this->getBlockMock();

        $this->assertFalse($blockMock->isBrief());
    }

    /**
     * Test that menu block doesn't visible, when menu_brief flag is false.
     */
    public function testIsBriefMenuVisibilityIsFalse()
    {
        $nodeParams = [
            'menu_visibility' => 0,
            'menu_brief' => 1,
        ];

        $nodeMock = $this->getMockBuilder(\Magento\VersionsCms\Model\Hierarchy\Node::class)
            ->disableOriginalConstructor()
            ->getMock();
        $nodeMock->expects($this->once())
            ->method('getMetadataContextMenuParams')
            ->willReturn($nodeParams);

        $this->currentNodeResolverMock->expects($this->once())
            ->method('get')
            ->with($this->requestMock)
            ->willReturn($nodeMock);

        $blockMock = $this->getBlockMock();

        $this->assertFalse($blockMock->isBrief());
    }

    /**
     * Test that brief params is null, if menu_brief is not set.
     */
    public function testIsBriefParamsIsNull()
    {
        $nodeMock = $this->getMockBuilder(\Magento\VersionsCms\Model\Hierarchy\Node::class)
            ->disableOriginalConstructor()
            ->getMock();
        $nodeMock->expects($this->once())
            ->method('getMetadataContextMenuParams')
            ->willReturn(null);

        $this->currentNodeResolverMock->expects($this->once())
            ->method('get')
            ->with($this->requestMock)
            ->willReturn($nodeMock);

        $blockMock = $this->getBlockMock();

        $this->assertFalse($blockMock->isBrief());
    }

    /**
     * Create Hierarchy Menu mock object
     *
     * Helper methods, that provides unified logic of creation of Hierarchy Menu mock object,
     * required to implement test iterations.
     *
     * @return \Magento\VersionsCms\Block\Hierarchy\Menu|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getBlockMock()
    {
        $blockMock = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))
            ->getObject(
                \Magento\VersionsCms\Block\Hierarchy\Menu::class,
                [
                    'nodeFactory' => $this->nodeFactoryMock,
                    'currentNodeResolver' => $this->currentNodeResolverMock,
                    'context' => $this->contextMock,
                ]
            );

        return $blockMock;
    }
}
