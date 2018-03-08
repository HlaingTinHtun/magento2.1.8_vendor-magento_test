<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogRuleStaging\Test\Unit\Block\Adminhtml\Update;

use Magento\CatalogRuleStaging\Block\Adminhtml\Update\Provider;
use Magento\Framework\Exception\NoSuchEntityException;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var Provider
     */
    protected $button;

    protected function setUp()
    {
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->ruleRepositoryMock = $this->getMock(
            'Magento\CatalogRule\Api\CatalogRuleRepositoryInterface'
        );
        $this->button = new Provider($this->requestMock, $this->ruleRepositoryMock);
    }

    public function testGetRuleIdNoRule()
    {
        $this->ruleRepositoryMock->expects($this->once())
            ->method('get')
            ->willThrowException(new NoSuchEntityException(__('Smth went to exception')));
        $this->assertNull($this->button->getId());
    }

    public function testGetRuleId()
    {
        $id = 203040;
        $catalogRuleMock = $this->getMock('Magento\CatalogRule\Api\Data\RuleInterface');

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($id);
        $this->ruleRepositoryMock->expects($this->once())
            ->method('get')
            ->with($id)
            ->willReturn($catalogRuleMock);
        $catalogRuleMock->expects($this->once())->method('getRuleId')->willReturn($id);

        $this->assertEquals($id, $this->button->getId());
    }
}
