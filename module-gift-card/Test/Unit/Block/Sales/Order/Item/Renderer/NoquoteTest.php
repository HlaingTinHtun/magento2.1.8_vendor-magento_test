<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCard\Test\Unit\Block\Sales\Order\Item\Renderer;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class NoquoteTest
 */
class NoquoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCard\Block\Sales\Order\Item\Renderer\Noquote
     */
    private $block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $layoutMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $priceRenderBlock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $itemMock;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->layoutMock = $this->getMockBuilder(\Magento\Framework\View\Layout::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->priceRenderBlock = $this->getMockBuilder(\Magento\Backend\Block\Template::class)
            ->disableOriginalConstructor()
            ->setMethods(['setItem', 'toHtml'])
            ->getMock();

        $this->itemMock = $this->getMockBuilder(\Magento\Sales\Model\Order\Item::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->block = $helper->getObject(
            \Magento\GiftCard\Block\Sales\Order\Item\Renderer\Noquote::class,
            [
                'layout' => $this->layoutMock,
                'data' => [
                    'item' => $this->itemMock
                ]
            ]
        );
    }

    public function testGetItemPrice()
    {
        $html = '$42.42';

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('item_price')
            ->will($this->returnValue($this->priceRenderBlock));
        $this->priceRenderBlock->expects($this->once())
            ->method('setItem')
            ->with($this->itemMock);
        $this->priceRenderBlock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($html));

        $this->assertEquals($html, $this->block->getItemPrice($this->itemMock));
    }

    public function testGetItemOptions()
    {
        $data = [
            ['giftcard_sender_name', 'Sender Name'],
            ['giftcard_sender_email', 'sender@mail.com'],
            ['giftcard_recipient_name', 'Recipient Name'],
            ['giftcard_recipient_email', 'recipient@mail.com'],
            ['giftcard_message', 'Gift card message.'],
        ];
        $result = [
            0 => [
                'label' => __('Gift Card Sender'),
                'value' => 'Sender Name <sender@mail.com>'
            ],
            1 => [
                'label' => __('Gift Card Recipient'),
                'value' => 'Recipient Name <recipient@mail.com>'
            ],
            2 => [
                'label' => __('Gift Card Message'),
                'value' => 'Gift card message.'
            ],
        ];

        $this->itemMock->expects($this->any())
            ->method('getProductOptionByCode')
            ->willReturnMap($data);

        $this->assertEquals($result, $this->block->getItemOptions());
    }
}
