<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Model\Entity;

/**
 * Class RetrieverPool
 */
class RetrieverPool
{
    /**
     * @var RetrieverInterface[]
     */
    private $retrievers;

    /**
     * RetrieverPool constructor.
     *
     * @param RetrieverInterface[] $retrievers
     */
    public function __construct($retrievers = [])
    {
        $this->retrievers = $retrievers;
    }

    /**
     * @param string $entityType
     * @return RetrieverInterface
     */
    public function getRetriever($entityType)
    {
        return $this->retrievers[$entityType];
    }
}
