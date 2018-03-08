<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Ui\Component\Listing\Column\Entity;

interface UrlProviderInterface
{
    /**
     * Get URL for data provider item
     *
     * @param array $item
     * @return string
     */
    public function getUrl(array $item);
}
