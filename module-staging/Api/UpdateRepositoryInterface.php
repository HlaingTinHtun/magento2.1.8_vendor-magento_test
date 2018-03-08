<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Api;

/**
 * Update repository interface.
 * @api
 */
interface UpdateRepositoryInterface
{
    /**
     * Lists updates that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magento\Staging\Api\Data\UpdateSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria);

    /**
     * Loads a specified update.
     *
     * @param int $id
     * @return \Magento\Staging\Api\Data\UpdateInterface
     */
    public function get($id);

    /**
     * Deletes a specified update.
     *
     * @param \Magento\Staging\Api\Data\UpdateInterface $entity
     * @return bool
     */
    public function delete(\Magento\Staging\Api\Data\UpdateInterface $entity);

    /**
     * Performs persist operations for a specified update.
     *
     * @param \Magento\Staging\Api\Data\UpdateInterface $entity
     * @return \Magento\Staging\Api\Data\UpdateInterface
     */
    public function save(\Magento\Staging\Api\Data\UpdateInterface $entity);

    /**
     * Get max version id for requested time
     *
     * @param int $timestamp
     * @return string|null
     */
    public function getVersionMaxIdByTime($timestamp);
}
