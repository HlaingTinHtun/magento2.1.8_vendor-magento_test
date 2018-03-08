<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Model\Update;

use Magento\Staging\Api\Data\UpdateInterface;
use Magento\Staging\Model\Update\Includes\Retriever as IncludesRetriever;
use Magento\Staging\Model\UpdateRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Staging\Model\VersionHistoryInterface;

class Cleaner
{
    /**
     * @var UpdateRepository
     */
    private $updateRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var IncludesRetriever
     */
    private $includes;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var VersionHistoryInterface
     */
    private $versionHistory;

    /**
     * @param UpdateRepository $updateRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param IncludesRetriever $includes
     * @param FilterBuilder $filterBuilder
     * @param VersionHistoryInterface $versionHistory
     */
    public function __construct(
        UpdateRepository $updateRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        IncludesRetriever $includes,
        FilterBuilder $filterBuilder,
        VersionHistoryInterface $versionHistory
    ) {
        $this->updateRepository = $updateRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->includes = $includes;
        $this->filterBuilder = $filterBuilder;
        $this->versionHistory = $versionHistory;
    }

    /**
     * Remove empty updates
     *
     * @return void
     */
    public function execute()
    {
        $updateList = $this->getUpdateList();
        $movedIds = $this->getMovedToUpdateIds();
        $updateIds = array_map(function (UpdateInterface $item) {
            return $item->getId();
        }, $updateList->getItems());

        $updateIds = array_diff($updateIds, $movedIds);

        $includes = $this->includes->getIncludes($updateIds);
        $notEmptyUpdates = array_unique(array_column($includes, 'created_in'));
        $idsToDelete = array_diff($updateIds, $notEmptyUpdates);
        $updatesToDelete = array_filter($updateList->getItems(), function (UpdateInterface $update) use ($idsToDelete) {
            return in_array($update->getId(), $idsToDelete);
        });
        foreach ($updatesToDelete as $update) {
            $this->updateRepository->delete($update);
        }
    }

    /**
     * @return \Magento\Staging\Api\Data\UpdateSearchResultInterface
     */
    private function getUpdateList()
    {
        $this->searchCriteriaBuilder->addFilters(
            [
                //exclude campaigns which were changed but not synchronized with their entities yet
                $this->filterBuilder
                    ->setField('moved_to')
                    ->setConditionType('null')
                    ->create(),
                $this->filterBuilder
                    ->setField('is_rollback')
                    ->setConditionType('null')
                    ->create(),
                // exclude active update
                $this->filterBuilder
                    ->setField('id')
                    ->setConditionType('neq')
                    ->setValue($this->versionHistory->getCurrentId())
                    ->create()
            ]
        );
        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->updateRepository->getList($searchCriteria);
    }

    /**
     * @return array
     */
    private function getMovedToUpdateIds()
    {
        $this->searchCriteriaBuilder->addFilters(
            [
                $this->filterBuilder
                    ->setField('moved_to')
                    ->setConditionType('notnull')
                    ->create()
            ]
        );
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $list = [];
        foreach ($this->updateRepository->getList($searchCriteria) as $item) {
            $list[] = $item->getMovedTo();
        }
        return $list;
    }
}
