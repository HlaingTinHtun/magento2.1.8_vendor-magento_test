<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftWrapping\Test\Unit\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\GiftWrapping\Model\Wrapping;

class WrappingTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GiftWrapping\Model\Wrapping */
    protected $wrapping;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\Filesystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $filesystemMock;

    /** @var string */
    protected $testImagePath;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mediaDirectoryMock;

    protected function setUp()
    {
        $this->filesystemMock = $this->getMock('Magento\Framework\Filesystem', [], [], '', false);
        $this->mediaDirectoryMock = $this->getMockBuilder('Magento\Framework\Filesystem\Directory\WriteInterface')
            ->disableOriginalConstructor()->setMethods([])->getMock();
        $this->filesystemMock->expects($this->once())->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)->will($this->returnValue($this->mediaDirectoryMock));

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->wrapping = $this->objectManagerHelper->getObject(
            'Magento\GiftWrapping\Model\Wrapping',
            [
                'filesystem' => $this->filesystemMock,
            ]
        );
        $this->testImagePath = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'magento_image.jpg';
    }

    public function testAttachBinaryImageEmptyFileName()
    {
        $this->assertFalse($this->wrapping->attachBinaryImage('', ''));
    }

    /**
     * @dataProvider invalidBinaryImageDataProvider
     * @param $fileName
     * @param $imageContent
     * @param $exceptionMessage
     */
    public function testAttachBinaryImageExceptions($fileName, $imageContent, $exceptionMessage)
    {
        $this->setExpectedException('Magento\Framework\Exception\InputException', $exceptionMessage);
        $this->wrapping->attachBinaryImage($fileName, $imageContent);
    }

    /**
     * @return array
     */
    public function invalidBinaryImageDataProvider()
    {
        return [
            ['image.php', 'image content', 'The image extension "php" not allowed'],
            ['{}?*:<>.jpg', 'image content', 'Provided image name contains forbidden characters'],
            ['image.jpg', 'image content', 'The image content must be valid data'],
            ['image.jpg', '', 'The image content must be valid data']
        ];
    }

    public function testAttachBinaryImageWriteFail()
    {
        $imageContent = file_get_contents($this->testImagePath);
        list($fileName, $imageContent, $absolutePath, $result) = ['image.jpg', $imageContent, 'absolutePath', false];
        $this->mediaDirectoryMock->expects($this->once())->method('getAbsolutePath')
            ->with(Wrapping::IMAGE_PATH . $fileName)->will($this->returnValue($absolutePath));
        $this->mediaDirectoryMock->expects($this->once())->method('writeFile')
            ->with(Wrapping::IMAGE_TMP_PATH . $absolutePath, $imageContent)->will($this->returnValue($result));
        $this->assertFalse($this->wrapping->attachBinaryImage($fileName, $imageContent));
    }

    public function testAttachBinaryImage()
    {
        $imageContent = file_get_contents($this->testImagePath);
        list($fileName, $imageContent, $absolutePath, $result) = ['image.jpg', $imageContent, 'absolutePath', true];
        $this->mediaDirectoryMock->expects($this->once())->method('getAbsolutePath')
            ->with(Wrapping::IMAGE_PATH . $fileName)->will($this->returnValue($absolutePath));
        $this->mediaDirectoryMock->expects($this->once())->method('writeFile')
            ->with(Wrapping::IMAGE_TMP_PATH . $absolutePath, $imageContent)->will($this->returnValue($result));

        $this->assertEquals($absolutePath, $this->wrapping->attachBinaryImage($fileName, $imageContent));
        $this->assertEquals($fileName, $this->wrapping->getData('tmp_image'));
        $this->assertEquals($fileName, $this->wrapping->getData('image'));
    }

    public function testGetBaseCurrencyCodeWhenItNotExists()
    {
        $storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $store = $this->getMock('Magento\Store\Model\System\Store', ['getBaseCurrencyCode', '__wakeUp'], [], '', false);
        $wrapping = $this->objectManagerHelper->getObject(
            'Magento\GiftWrapping\Model\Wrapping',
            [
                'storeManager' => $storeManager
            ]
        );
        $storeManager->expects($this->once())->method('getStore')->willReturn($store);
        $store->expects($this->once())->method('getBaseCurrencyCode')->willReturn('USD');
        $this->assertEquals('USD', $wrapping->getBaseCurrencyCode());
    }

    public function testGetBaseCurrencyCode()
    {
        $storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $wrapping = $this->objectManagerHelper->getObject(
            'Magento\GiftWrapping\Model\Wrapping',
            [
                'storeManager' => $storeManager,
                'data' =>[
                    'base_currency_code' =>'EU'
                ]
            ]
        );
        $storeManager->expects($this->never())->method('getStore');
        $this->assertEquals('EU', $wrapping->getBaseCurrencyCode());
    }
}
