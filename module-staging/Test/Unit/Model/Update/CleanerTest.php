<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Model\Update;

use Magento\Staging\Model\Update;
use Magento\Staging\Model\Update\Includes\Retriever as IncludesRetriever;
use Magento\Staging\Model\UpdateRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Staging\Model\Update\Cleaner;

class CleanerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $updateRepository;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SearchCriteria|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchCriteria;

    /**
     * @var \Magento\Staging\Api\Data\UpdateSearchResultInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchResult;

    /**
     * @var IncludesRetriever|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $includesRetriever;

    /**
     * @var Update|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $update;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $filterBuilderMock;

    /**
     * @var \Magento\Staging\Model\VersionHistoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $versionHistoryMock;

    /**
     * @var Cleaner
     */
    protected $cleaner;

    public function setUp()
    {
        $this->updateRepository = $this->getMockBuilder('Magento\Staging\Model\UpdateRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilder = $this->getMockBuilder('Magento\Framework\Api\SearchCriteriaBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteria = $this->getMockBuilder('Magento\Framework\Api\SearchCriteria')
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResult = $this->getMockBuilder('Magento\Staging\Api\Data\UpdateSearchResultInterface')
            ->setMethods(['getItems'])
            ->getMockForAbstractClass();
        $this->includesRetriever = $this->getMockBuilder('Magento\Staging\Model\Update\Includes\Retriever')
            ->disableOriginalConstructor()
            ->getMock();
        $this->update = $this->getMockBuilder('Magento\Staging\Model\Update')
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterBuilderMock = $this->getMockBuilder('Magento\Framework\Api\FilterBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->versionHistoryMock = $this->getMockBuilder('Magento\Staging\Model\VersionHistoryInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->cleaner = new Cleaner(
            $this->updateRepository,
            $this->searchCriteriaBuilder,
            $this->includesRetriever,
            $this->filterBuilderMock,
            $this->versionHistoryMock
        );
    }

    public function testExecute()
    {
        $ids = [12];
        $currentVersionId = 123;
        $includes = [
            [
                'created_in' => 156
            ]
        ];
        $this->filterBuilderMock->expects($this->any())
            ->method('setField')
            ->withConsecutive(['moved_to'], ['is_rollback'], ['id'], ['moved_to'])
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->any())
            ->method('setConditionType')
            ->withConsecutive(['null'], ['null'], ['neq'], ['notnull'])
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->any())
            ->method('create')
            ->withAnyParameters()
            ->willReturn([]);
        $this->filterBuilderMock->expects($this->any())
            ->method('setValue')
            ->with($currentVersionId)
            ->willReturnSelf();
        $this->versionHistoryMock->expects($this->once())
            ->method('getCurrentId')
            ->willReturn($currentVersionId);
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('create')
            ->withAnyParameters()
            ->willReturn($this->searchCriteria);
        $this->updateRepository->expects($this->any())
            ->method('getList')
            ->with($this->searchCriteria)
            ->willReturn($this->searchResult);
        $this->includesRetriever->expects($this->once())
            ->method('getIncludes')
            ->with($ids)
            ->willReturn($includes);
        $this->searchResult->expects($this->exactly(2))
            ->method('getItems')
            ->willReturn([$this->update]);
        $this->update->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(12);
        $this->update->expects($this->never())
            ->method('getIsCampaign')
            ->willReturn(false);
        $this->updateRepository->expects($this->once())
            ->method('delete')
            ->with($this->update);
        $this->cleaner->execute();
    }
}
