<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\ForeignKey\Test\Unit\Config;

use Magento\Framework\Stdlib\BooleanUtils;
use Magento\Framework\ForeignKey\Config\Converter;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Converter
     */
    private $model;

    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceConfigMock;

    /**
     * @var string
     */
    private $fixturePath;

    protected function setUp()
    {
        $this->resourceConfigMock = $this->getMock('Magento\Framework\App\ResourceConnection\ConfigInterface');
        $this->model = new Converter($this->resourceConfigMock, new BooleanUtils());
        $this->fixturePath = realpath(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
    }

    public function testConvert()
    {
        $this->resourceConfigMock->expects($this->any())
            ->method('getConnectionName')
            ->willReturnCallback(function ($resourceName) {
                return $resourceName;
            });
        $dom = new \DOMDocument();
        $dom->load($this->fixturePath . 'constraints.xml');
        $constraints = $this->model->convert($dom);
        $expectedResult = require $this->fixturePath . 'constraints.php';
        $this->assertEquals($expectedResult, $constraints);
    }
}