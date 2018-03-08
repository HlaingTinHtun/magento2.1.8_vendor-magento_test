<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Model;

use Magento\Staging\Api\Data\UpdateSearchResultInterfaceFactory as SearchResultFactory;
use Magento\Staging\Api\UpdateRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Staging\Model\ResourceModel\Update as UpdateResource;
use Magento\Staging\Api\Data\UpdateInterface;
use Magento\Staging\Model\Update\Validator;
use Magento\Staging\Model\VersionHistoryInterface;

/**
 * Class UpdateRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpdateRepository implements UpdateRepositoryInterface
{
    /**
     * @var UpdateInterface[]
     */
    protected $registry = [];

    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory;

    /**
     * @var UpdateResource
     */
    protected $resource;

    /**
     * @var UpdateFactory
     */
    protected $updateFactory;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var VersionHistoryInterface
     */
    protected $versionHistory;

    /**
     * Repository constructor
     *
     * @param SearchResultFactory $searchResultFactory
     * @param UpdateResource $resource
     * @param UpdateFactory $updateFactory
     * @param Validator $validator
     * @param VersionHistoryInterface $versionHistory
     */
    public function __construct(
        SearchResultFactory $searchResultFactory,
        UpdateResource $resource,
        UpdateFactory $updateFactory,
        Validator $validator,
        VersionHistoryInterface $versionHistory
    ) {
        $this->searchResultFactory = $searchResultFactory;
        $this->resource = $resource;
        $this->updateFactory = $updateFactory;
        $this->validator = $validator;
        $this->versionHistory = $versionHistory;
    }

    /**
     * Loads a specified update.
     *
     * @param int $id
     * @return UpdateInterface
     * @throws NoSuchEntityException
     */
    public function get($id)
    {
        if (!isset($this->registry[$id])) {
            /** @var Update $update */
            $update = $this->updateFactory->create();
            if ($id == 1) {
                $update->setId($id);
            } else {
                $this->resource->load($update, $id);
                if (!$update->getId()) {
                    throw new NoSuchEntityException(__('Update with id "%1" does not exist.', $id));
                }
                if ($update->getRollbackId()) {
                    $update->setEndTime($this->get($update->getRollbackId())->getStartTime());
                }
            }
            $this->registry[$id] = $update;
        }

        return $this->registry[$id];
    }

    /**
     * Lists updates that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magento\Staging\Api\Data\UpdateSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $searchResult = $this->searchResultFactory->create();
        $searchResult->setSearchCriteria($criteria);
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $searchResult->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResult->setCurPage($criteria->getCurrentPage());
        $searchResult->setPageSize($criteria->getPageSize());
        return $searchResult;
    }

    /**
     * Deletes a specified update.
     *
     * @param UpdateInterface $entity
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(UpdateInterface $entity)
    {
        try {
            if ($this->versionHistory->getCurrentId() == $entity->getId()) {
                throw new CouldNotDeleteException(__('Active update can not be deleted'));
            }
            if ($entity->getRollbackId()) {
                $this->resource->delete($this->get($entity->getRollbackId()));
            }
            $this->resource->delete($entity);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Performs persist operations for a specified update.
     *
     * @param UpdateInterface $entity
     * @return UpdateInterface
     * @throws CouldNotSaveException
     */
    public function save(UpdateInterface $entity)
    {
        try {
            if (!$entity->getId()) {
                $this->validator->validateCreate($entity);
                $entity->setId($this->getIdForEntity($entity));
                $entity->isObjectNew(true);
            } else {
                $this->validator->validateUpdate($entity);
                $oldUpdate = $this->updateFactory->create();
                $id = $entity->getId();
                $this->resource->load($oldUpdate, $id);
                if (strtotime($entity->getStartTime()) != strtotime($oldUpdate->getStartTime())) {
                    if ($id <= $this->versionHistory->getCurrentId()) {
                        throw new ValidatorException(__('Start time could not be changed while update active'));
                    }
                    $entity->setOldId($oldUpdate->getId());
                    $entity->setId($this->getIdForEntity($entity));
                }
            }
            if ($entity->getEndTime()) {
                $entity->setRollbackId($this->getRollback($entity));
            } elseif ($entity->getRollbackId()) {
                $this->delete($this->get($entity->getRollbackId()));
                $entity->setRollbackId(null);
            }
            $this->resource->save($entity);
        } catch (ValidatorException $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save Future Update.'));
        }
        return $entity;
    }

    /**
     * @param UpdateInterface $entity
     * @return int
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    protected function getRollback(UpdateInterface $entity)
    {
        if ($entity->getRollbackId()) {
            $rollback = $this->get($entity->getRollbackId());
            $rollback->setStartTime($entity->getEndTime());
        } else {
            $rollback = $this->updateFactory->create();
            $rollback->setName(sprintf('Rollback for "%s"', $entity->getName()));
            $rollback->setStartTime($entity->getEndTime());
            $rollback->setIsRollback(true);
        }
        $rollback = $this->save($rollback);
        return $rollback->getId();
    }

    /**
     * @param UpdateInterface $entity
     * @return int
     */
    protected function getIdForEntity(UpdateInterface $entity)
    {
        $timestamp = strtotime($entity->getStartTime());
        try {
            $this->get($timestamp);
            while (true) {
                $this->get(++$timestamp);
            }
        } catch (NoSuchEntityException $e) {
            return $timestamp;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionMaxIdByTime($timestamp)
    {
        return $this->resource->getMaxIdByTime($timestamp);
    }
}
