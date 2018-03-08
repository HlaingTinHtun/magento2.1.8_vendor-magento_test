<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Model\Entity;

use Magento\Framework\DataObject;

interface BuilderInterface
{
    /**
     * Build entity by prototype
     *
     * @param object $prototype
     * @return object
     */
    public function build($prototype);
}
