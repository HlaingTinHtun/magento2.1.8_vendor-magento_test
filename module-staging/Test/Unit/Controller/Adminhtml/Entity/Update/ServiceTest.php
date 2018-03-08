<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Controller\Adminhtml\Entity\Update;

use Magento\Staging\Controller\Adminhtml\Entity\Update\Service;
use Magento\Staging\Api\Data\UpdateInterface;

class ServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataPoolMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $updateRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $updateFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $versionManagerMock;

    /**
     * @var Service
     */
    private $service;

    protected function setUp()
    {
        $this->metadataPoolMock = $this->getMock('Magento\Framework\EntityManager\MetadataPool', [], [], '', false);
        $this->updateRepositoryMock = $this->getMock('Magento\Staging\Api\UpdateRepositoryInterface');
        $this->updateFactoryMock = $this->getMock('Magento\Staging\Model\UpdateFactory', ['create'], [], '', false);
        $this->versionManagerMock = $this->getMock('Magento\Staging\Model\VersionManager', [], [], '', false);

        $this->service = new Service(
            $this->metadataPoolMock,
            $this->updateRepositoryMock,
            $this->updateFactoryMock,
            $this->versionManagerMock
        );
    }

    public function testCreateUpdateCreatesUpdate()
    {
        $updateData = [];
        $updateMock = $this->getMock('Magento\Staging\Model\Update', [], [], '', false);
        $hydratorMock = $this->getMock(
            'Magento\Framework\Model\Entity\Hydrator',
            ['extract', 'hydrate'],
            [],
            '',
            false
        );

        $this->updateFactoryMock->expects($this->once())->method('create')->willReturn($updateMock);
        $this->metadataPoolMock->expects($this->any())
            ->method('getHydrator')
            ->with(UpdateInterface::class)
            ->willReturn($hydratorMock);
        $hydratorMock->expects($this->once())->method('hydrate')->with($updateMock, $updateData);
        $updateMock->expects($this->once())->method('setIsCampaign')->with(false);
        $this->updateRepositoryMock->expects($this->once())->method('save')->with($updateMock);

        $this->assertEquals($updateMock, $this->service->createUpdate($updateData));
    }

    public function testEditUpdateSavesEditedUpdate()
    {
        $updateId = 1;
        $startTime = '01/24/2016 09:00';
        $endTime = '02/24/2016 09:00';
        $updateData = [
            'update_id' => $updateId,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
        $updateMock = $this->getMock('Magento\Staging\Model\Update', [], [], '', false);
        $hydratorMock = $this->getMock(
            'Magento\Framework\Model\Entity\Hydrator',
            ['extract', 'hydrate'],
            [],
            '',
            false
        );

        $this->updateRepositoryMock->expects($this->any())->method('get')->with($updateId)->willReturn($updateMock);
        $updateMock->expects($this->once())->method('getStartTime')->willReturn($startTime);
        $updateMock->expects($this->any())->method('getEndTime')->willReturn($endTime);
        $this->metadataPoolMock->expects($this->atLeastOnce())
            ->method('getHydrator')
            ->with(UpdateInterface::class)
            ->willReturn($hydratorMock);
        $hydratorMock->expects($this->once())->method('hydrate')->with($updateMock, $updateData);
        $this->updateRepositoryMock->expects($this->once())->method('save')->with($updateMock);

        $this->assertEquals($updateMock, $this->service->editUpdate($updateData));
    }

    public function testEditUpdateSavesCreatesUpdate()
    {
        $updateId = 1;
        $startTime = '01/24/2016 09:00';
        $endTime = '02/24/2016 09:00';
        $updateStart = '02/24/2016 10:00';
        $updateEnd = '05/24/2016 10:00';
        $updateData = [
            'update_id' => $updateId,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
        $updateMock = $this->getMock('Magento\Staging\Model\Update', [], [], '', false);
        $hydratorMock = $this->getMock(
            'Magento\Framework\Model\Entity\Hydrator',
            ['extract', 'hydrate'],
            [],
            '',
            false
        );

        $this->updateRepositoryMock->expects($this->any())->method('get')->with($updateId)->willReturn($updateMock);
        $updateMock->expects($this->once())->method('getStartTime')->willReturn($updateStart);
        $this->metadataPoolMock->expects($this->once())
            ->method('getHydrator')
            ->with(UpdateInterface::class)
            ->willReturn($hydratorMock);

        $this->updateFactoryMock->expects($this->once())->method('create')->willReturn($updateMock);
        $this->assertEquals($updateMock, $this->service->editUpdate($updateData));
    }

    public function testAssignedUpdateRetrievesCorrespondingUpdateIfInputParametersAreValid()
    {
        $updateId = 1;
        $updateData = [
            'select_id' => [
                [
                    'id' => $updateId,
                ],
            ],
        ];
        $updateMock = $this->getMock('Magento\Staging\Model\Update', [], [], '', false);
        $this->updateRepositoryMock
            ->expects($this->any())
            ->method('get')
            ->with($updateData['select_id'])
            ->willReturn($updateMock);

        $this->assertEquals($updateMock, $this->service->assignUpdate($updateData));
    }

    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage The 'select_id' value is required.
     */
    public function testAssignedUpdateThrowsExceptionIfInputParametersAreInvalid()
    {
        $updateData = [];
        $this->updateRepositoryMock->expects($this->never())->method('get');

        $this->service->assignUpdate($updateData);
    }

    /**
     * @return array
     */
    public function prepareDataForEditUpdateSavesEditedUpdate()
    {
        return [
            //changing only start time
            ['01/24/2016 09:00', '03/24/2016 09:00', '02/24/2016 09:00', '03/24/2016 09:00', true],
            //changing only end time
            ['01/24/2016 09:00', '03/24/2016 09:00', '01/24/2016 09:00', '04/24/2016 09:00', true],
            //changing both times
            ['01/24/2016 09:00', '03/24/2016 09:00', '02/24/2016 09:00', '04/24/2016 09:00', true],
            //change nothings
            ['01/24/2016 09:00', '03/24/2016 09:00', '01/24/2016 09:00', '03/24/2016 09:00', false],
        ];
    }

    /**
     * @param string $startTime
     * @param string $endTime
     * @param string $updateStartTime
     * @param string $updateEndTime
     * @param boolean $shouldCreateUpdate
     * @dataProvider prepareDataForEditUpdateSavesEditedUpdate
     */
    public function testEditUpdate($startTime, $endTime, $updateStartTime, $updateEndTime, $shouldCreateUpdate)
    {
        $updateId = 1;
        $updateData = [
            'update_id' => $updateId,
            'start_time' => $updateStartTime,
            'end_time' => $updateEndTime
        ];
        $update = $this->getMockBuilder(\Magento\Staging\Api\Data\UpdateInterface::class)->getMock();
        $this->updateRepositoryMock
            ->expects($this->once())
            ->method("get")
            ->with($updateId)
            ->willReturn($update);

        $update
            ->expects($this->atLeastOnce())
            ->method("getStartTime")
            ->willReturn($startTime);

        $update
            ->expects($this->any())
            ->method("getEndTime")
            ->willReturn($endTime);
        //Handle Hydrator
        $hydratorMock = $this->getMock(
            'Magento\Framework\Model\Entity\Hydrator',
            ['extract', 'hydrate'],
            [],
            '',
            false
        );

        $this->metadataPoolMock->expects($this->once())
            ->method('getHydrator')
            ->with(UpdateInterface::class)
            ->willReturn($hydratorMock);

        //Main Logic: Whether we should edit our update or drop exist and create new one
        if ($shouldCreateUpdate) {
            $this->updateFactoryMock
                ->expects($this->once())
                ->method("create")
                ->willReturn($update);
        } else {
            $this->updateFactoryMock
                ->expects($this->never())
                ->method("create");
        }

        $this->updateRepositoryMock
            ->expects($this->once())
            ->method("save")
            ->with($update);

        $this->assertEquals($update, $this->service->editUpdate($updateData));
    }
}
