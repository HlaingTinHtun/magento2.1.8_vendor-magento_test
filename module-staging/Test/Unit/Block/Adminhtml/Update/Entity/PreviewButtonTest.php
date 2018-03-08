<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Block\Adminhtml\Update\Entity;

use Magento\Staging\Block\Adminhtml\Update\Entity\PreviewButton;

class PreviewButtonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $updateIdProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderMock;

    /**
     * @var PreviewButton
     */
    protected $button;

    protected function setUp()
    {
        $this->entityProviderMock = $this->getMock(
            'Magento\Staging\Block\Adminhtml\Update\Entity\EntityProviderInterface',
            []
        );

        $this->updateIdProviderMock = $this->getMock(
            'Magento\Staging\Block\Adminhtml\Update\IdProvider',
            [],
            [],
            '',
            false
        );

        $this->urlBuilderMock = $this->getMock(
            '\Magento\Staging\Model\Preview\UrlBuilder',
            [],
            [],
            '',
            false
        );
        $this->button = new PreviewButton(
            $this->entityProviderMock,
            $this->updateIdProviderMock,
            $this->urlBuilderMock
        );
    }

    public function testGetButtonDataNoUpdate()
    {
        $this->updateIdProviderMock->expects($this->once())->method('getUpdateId')->willReturn(null);
        $this->assertEmpty($this->button->getButtonData());
    }

    public function testGetButtonData()
    {
        $checkFields = ['label', 'url', 'sort_order'];
        $updateId = 223335;
        $this->updateIdProviderMock->expects($this->exactly(3))->method('getUpdateId')->willReturn($updateId);
        $this->entityProviderMock->expects($this->once())->method('getUrl');
        $this->urlBuilderMock->expects($this->once())->method('getPreviewUrl');

        $result = $this->button->getButtonData();
        foreach ($checkFields as $field) {
            $this->assertArrayHasKey($field, $result);
        }
    }
}
