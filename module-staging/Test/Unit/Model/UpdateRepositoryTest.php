<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Model;

class UpdateRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Staging\Model\UpdateRepository
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Staging\Model\Update
     */
    protected $entityMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Staging\Model\ResourceModel\Update
     */
    protected $resourceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $updateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $updateFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Staging\Model\Update\Validator
     */
    protected $validatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $versionHistoryMock;

    protected function setUp()
    {
        $this->entityMock = $this->getMock('\Magento\Staging\Model\Update', [], [], '', false);
        $this->resourceMock = $this->getMock('\Magento\Staging\Model\ResourceModel\Update', [], [], '', false);
        $this->validatorMock = $this->getMock('\Magento\Staging\Model\Update\Validator', [], [], '', false);
        $this->updateMock = $this->getMock('Magento\Staging\Model\Update', [], [], '', false);
        $this->updateFactoryMock = $this->getMock('Magento\Staging\Model\UpdateFactory', ['create'], [], '', false);
        $this->versionHistoryMock = $this->getMock('Magento\Staging\Model\VersionHistoryInterface', [], [], '', false);
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject(
            'Magento\Staging\Model\UpdateRepository',
            [
                'resource' => $this->resourceMock,
                'validator' => $this->validatorMock,
                'updateFactory' => $this->updateFactoryMock,
                'versionHistory' => $this->versionHistoryMock
            ]
        );
    }

    /**
     * test save new permanent campaign
     */
    public function testSaveNewPermanent()
    {
        $this->validatorMock->expects($this->once())->method('validateCreate')->with($this->entityMock);
        $this->entityMock->expects($this->once())->method('getId')->willReturn(null);
        $startTime = date('m/d/y', time());
        //getIdForEntity
        $this->entityMock->expects($this->any())->method('getStartTime')->willReturn($startTime);
        $this->updateFactoryMock->expects($this->any())->method('create')->willReturn($this->updateMock);
        $this->resourceMock->expects($this->any())->method('load')->with($this->updateMock, strtotime($startTime));
        $this->updateMock->expects($this->any())->method('getId')->willReturn(null);

        $this->entityMock->expects($this->once())->method('setId')->with(strtotime($startTime));
        $this->entityMock->expects($this->once())->method('isObjectNew')->with(true);
        $this->entityMock->expects($this->once())->method('getEndTime')->willReturn(null);
        $this->entityMock->expects($this->once())->method('getRollbackId')->willReturn(null);
        $this->resourceMock->expects($this->once())->method('save')->with($this->entityMock);

        $this->assertEquals($this->entityMock, $this->model->save($this->entityMock));
    }

    /**
     * @expectedExceptionMessage Start time could not be changed while update active
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSaveStartTimeActiveCampaign()
    {
        $startTime = date('m/d/y', time());
        $this->validatorMock->expects($this->once())->method('validateUpdate')->with($this->entityMock);
        $this->entityMock->expects($this->any())->method('getId')->willReturn(strtotime($startTime));
        //getIdForEntity
        $this->entityMock->expects($this->any())->method('getStartTime')->willReturn($startTime);
        $this->updateFactoryMock->expects($this->any())->method('create')->willReturn($this->updateMock);
        $this->resourceMock->expects($this->any())->method('load')->with($this->updateMock, strtotime($startTime));
        $this->updateMock->expects($this->any())->method('getId')->willReturn($startTime - 1);

        $this->versionHistoryMock->expects($this->any())
            ->method('getCurrentId')
            ->willReturn(strtotime($startTime) + 1);

        $this->assertNull($this->model->save($this->entityMock));
    }

    /**
     * test edit existed permanent campaign with changed Start Time
     */
    public function testSaveExistedPermanent()
    {
        $this->validatorMock->expects($this->once())->method('validateupdate')->with($this->entityMock);
        $oldStartTime = date('m/d/y', time());
        $startTime = date('m/d/y', time() + 24 * 60 * 60);
        $oldId = strtotime($oldStartTime);
        $newId = strtotime($startTime);
        $this->entityMock->expects($this->any())->method('getId')->willReturn($oldId);
        $this->entityMock->expects($this->any())->method('getStartTime')->willReturn($startTime);

        $oldEntityMock = $this->getMock('Magento\Staging\Model\Update', [], [], '', false);
        $oldEntityMock->expects($this->once())->method('getStartTime')->willReturn($oldStartTime);
        $oldEntityMock->expects($this->once())->method('getId')->willReturn(strtotime($oldStartTime));
        $this->updateFactoryMock->expects($this->any())
            ->method('create')
            ->willReturnOnConsecutiveCalls(
                $oldEntityMock,
                $this->updateMock
            );
        $this->resourceMock->expects($this->any())->method('load');
        $this->updateMock->expects($this->any())->method('getId')->willReturn(null);

        //$this->entityMock->expects($this->once())->method('setOldId')->with(strtotime($oldStartTime));
        $this->entityMock->expects($this->once())->method('getEndTime')->willReturn(null);
        $this->entityMock->expects($this->once())->method('setId')->willReturn($newId);
        $this->entityMock->expects($this->once())->method('getRollbackId')->willReturn(null);
        $this->resourceMock->expects($this->once())->method('save')->with($this->entityMock);

        $this->assertEquals($this->entityMock, $this->model->save($this->entityMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage Active update can not be deleted
     */
    public function testDeleteActiveUpdate()
    {
        $savedVersionId = 123;
        $this->entityMock->expects($this->once())
            ->method('getId')
            ->willReturn($savedVersionId);
        $this->versionHistoryMock->expects($this->once())
            ->method('getCurrentId')
            ->willReturn($savedVersionId);
        $this->entityMock->expects($this->never())
            ->method('delete');

        $this->model->delete($this->entityMock);
    }
}
