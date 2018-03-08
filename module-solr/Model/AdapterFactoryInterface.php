<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\Model;

interface AdapterFactoryInterface
{
    /**
     * Return search adapter
     *
     * @return \Magento\Solr\Model\Adapter\Solarium
     */
    public function createAdapter();
}
