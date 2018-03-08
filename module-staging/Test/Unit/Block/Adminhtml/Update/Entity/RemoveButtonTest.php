<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Block\Adminhtml\Update\Entity;

class RemoveButtonTest extends \PHPUnit_Framework_TestCase
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
    protected $idProviderMock;

    /**
     * @var \Magento\Staging\Block\Adminhtml\Update\Entity\RemoveButton
     */
    protected $button;

    protected $entityIdentifier = '123894';

    protected $jsRemoveModal = 'removeJsModal';

    protected $jsRemoveLoader = 'removeJsLoader';

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
        $this->button = new \Magento\Staging\Block\Adminhtml\Update\Entity\RemoveButton(
            $this->entityProviderMock,
            $this->updateIdProviderMock,
            $this->entityIdentifier,
            $this->jsRemoveModal,
            $this->jsRemoveLoader
        );
    }

    public function testGetButtonDataNoUpdate()
    {
        $this->updateIdProviderMock->expects($this->once())->method('getUpdateId')->willReturn(null);
        $this->assertEmpty($this->button->getButtonData());
    }

    public function testGetButtonData()
    {
        $checkFields = ['label', 'class', 'sort_order', 'data_attribute'];
        $updateId = 223335;
        $this->updateIdProviderMock->expects($this->exactly(2))->method('getUpdateId')->willReturn($updateId);
        $this->entityProviderMock->expects($this->once())->method('getId');

        $result = $this->button->getButtonData();
        foreach ($checkFields as $field) {
            $this->assertArrayHasKey($field, $result);
        }
    }
}
