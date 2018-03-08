<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Staging\Model\Operation\Update;

use Magento\Framework\EntityManager\Operation\Update\UpdateMain;
use Magento\Framework\EntityManager\Operation\Update\UpdateAttributes;
use Magento\Framework\EntityManager\Operation\Update\UpdateExtensions;

/**
 * Class UpdateEntityVersion
 */
class UpdateEntityVersion
{
    /**
     * @var UpdateMain
     */
    private $updateMain;

    /**
     * @var UpdateAttributes
     */
    private $updateAttributes;

    /**
     * @var UpdateExtensions
     */
    private $updateExtensions;

    /**
     * UpdateEntityVersion constructor.
     *
     * @param UpdateMain $updateMain
     * @param UpdateAttributes $updateAttributes
     * @param UpdateExtensions $updateExtensions
     */
    public function __construct(
        UpdateMain $updateMain,
        UpdateAttributes $updateAttributes,
        UpdateExtensions $updateExtensions
    ) {
        $this->updateMain = $updateMain;
        $this->updateExtensions = $updateExtensions;
        $this->updateAttributes = $updateAttributes;
    }

    public function execute($entity, $arguments = [])
    {
        $entity = $this->updateMain->execute($entity, $arguments);
        $entity = $this->updateAttributes->execute($entity, $arguments);
        $entity = $this->updateExtensions->execute($entity, $arguments);
        return $entity;
    }
}
