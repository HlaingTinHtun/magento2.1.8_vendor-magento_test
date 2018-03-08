<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\ForeignKey\Test\Unit\Config;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ForeignKey\Config\SchemaLocator
     */
    protected $schemaLocator;

    protected function setUp()
    {
        $this->schemaLocator = new \Magento\Framework\ForeignKey\Config\SchemaLocator();
    }

    public function testGetSchema()
    {
        $this->assertRegExp('/etc[\/\\\\]constraints.xsd/', $this->schemaLocator->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $this->assertRegExp('/etc[\/\\\\]constraints.xsd/', $this->schemaLocator->getPerFileSchema());
    }
}
