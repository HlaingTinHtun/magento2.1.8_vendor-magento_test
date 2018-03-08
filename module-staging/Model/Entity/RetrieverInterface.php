<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Model\Entity;

use Magento\Framework\DataObject;

interface RetrieverInterface
{
    /**
     * Retrieve entity by entity id
     *
     * @param string $entityId
     * @return DataObject
     */
    public function getEntity($entityId);
}
