<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ResourceConnections\Test\Unit\DB\ConnectionAdapter;

use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ResourceConnections\DB\Adapter\Pdo\MysqlProxy;
use Magento\ResourceConnections\DB\ConnectionAdapter\Mysql\Builder;
use Magento\Framework\App\Request\Http as RequestHttp;

/**
 * Class MysqlTest
 * @package Magento\ResourceConnections\Test\Unit\DB\ConnectionAdapter
 */
class MysqlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Stdlib\StringUtils|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stringUtilsMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateTimeMock;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\ResourceConnections\DB\ConnectionAdapter\Mysql\Builder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $builderMock;

    /**
     * @var \Magento\Framework\DB\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var array
     */
    protected $configArray = [];

    protected function setUp()
    {
        $this->builderMock = $this->getMock(Builder::class, ['build'], [], '', false);
        $this->dateTimeMock = $this->getMock(DateTime::class, [], [], '', false);
        $this->requestMock = $this->getMock(RequestHttp::class, ['isSafeMethod'], [], '', false);
        $this->stringUtilsMock = $this->getMock(StringUtils::class, [], [], '', false);
        $this->loggerMock = $this->getMock(LoggerInterface::class, [], [], '', false);
    }

    /**
     * Test that real adapter is returned for non-safe method
     */
    public function testInstantiationForNonSafeMethodWithoutSlave()
    {
        $config = [
            'host' => 'testHost',
            'active' => true,
            'initStatements' => 'SET NAMES utf8',
            'type' => 'pdo_mysql'
        ];
        $this->requestMock->expects($this->never())->method('isSafeMethod')->will($this->returnValue(false));
        $expectedClass = \Magento\Framework\DB\Adapter\Pdo\Mysql::class;
        $this->expectBuild(
            $expectedClass,
            $config,
            $config
        );
    }

    /**
     * Test that real adapter is returned for non-safe method even if slave is set
     */
    public function testInstantiationForSafeMethodWithSlave()
    {
        $config = [
            'host' => 'testHost',
            'active' => true,
            'initStatements' => 'SET NAMES utf8',
            'type' => 'pdo_mysql',
            'slave' => [
                'host' => 'slaveHost'
            ]
        ];
        $expectedBuildConfig = $config;
        unset($expectedBuildConfig['slave']);
        $this->requestMock->expects($this->once())->method('isSafeMethod')->will($this->returnValue(false));
        $expectedClass = \Magento\Framework\DB\Adapter\Pdo\Mysql::class;
        $this->expectBuild(
            $expectedClass,
            $config,
            $expectedBuildConfig
        );
    }

    /**
     * Test that real adapter is returned for safe method if slave is not set
     */
    public function testInstantiationForSafeRequestWithoutSlave()
    {
        $config = [
            'host' => 'testHost',
            'active' => true,
            'initStatements' => 'SET NAMES utf8',
            'type' => 'pdo_mysql',
        ];
        $this->requestMock->expects($this->never())->method('isSafeMethod');
        $expectedClass = \Magento\Framework\DB\Adapter\Pdo\Mysql::class;
        $this->expectBuild(
            $expectedClass,
            $config,
            $config
        );
    }

    /**
     * Test that adapter proxy is returned for safe method if slave config is set
     */
    public function testInstantiationForSafeRequestWithSlave()
    {
        $config = [
            'host' => 'testHost',
            'active' => true,
            'initStatements' => 'SET NAMES utf8',
            'type' => 'pdo_mysql',
            'slave' => [
                'host' => 'slaveHost'
            ]
        ];
        $this->requestMock->expects($this->once())->method('isSafeMethod')->will($this->returnValue(true));
        $expectedClass = MysqlProxy::class;
        $this->expectBuild(
            $expectedClass,
            $config,
            $config
        );
    }

    /**
     * @param $config
     * @return \Magento\ResourceConnections\DB\ConnectionAdapter\Mysql
     */
    protected function createConnectionAdapter($config, \PHPUnit_Framework_MockObject_MockObject $selectFactoryMock)
    {
        $connectionAdapter = new \Magento\ResourceConnections\DB\ConnectionAdapter\Mysql(
            $this->stringUtilsMock,
            $this->dateTimeMock,
            $selectFactoryMock,
            $config,
            $this->requestMock,
            $this->builderMock
        );

        return $connectionAdapter;
    }

    /**
     * @param string $expectedClass
     * @param array $config
     * @param array $expectedConfig
     * @return void
     */
    protected function expectBuild($expectedClass, array $config, array $expectedConfig)
    {
        $selectFactory = $this->getMock(
            'Magento\Framework\DB\SelectFactory',
            [],
            [],
            '',
            false
        );
        $this->builderMock->expects($this->once())->method('build')->with(
            $expectedClass,
            $this->stringUtilsMock,
            $this->dateTimeMock,
            $this->loggerMock,
            $selectFactory,
            $expectedConfig
        );
        $connectionAdapter = $this->createConnectionAdapter(
            $config,
            $selectFactory
        );
        $connectionAdapter->getConnection($this->loggerMock);
    }
}
