<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ResourceConnections\DB\ConnectionAdapter\Mysql;

use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\SelectFactory;

class Builder
{
    /**
     * Build connection instance
     *
     * @param string $instanceName
     * @param StringUtils $stringUtils
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     * @param SelectFactory $selectFactory
     * @param array $config
     * @return Mysql
     */
    public function build(
        $instanceName,
        StringUtils $stringUtils,
        DateTime $dateTime,
        LoggerInterface $logger,
        SelectFactory $selectFactory,
        array $config
    ) {
        if (!in_array(Mysql::class, class_parents($instanceName, true) + [$instanceName => $instanceName])) {
            throw new \InvalidArgumentException('Invalid instance creation attempt. Class must extend ' . Mysql::class);
        }
        return new $instanceName($stringUtils, $dateTime, $logger, $selectFactory, $config, $this);
    }
}
