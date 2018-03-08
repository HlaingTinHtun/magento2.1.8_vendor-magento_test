<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Model\Update;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Staging\Api\Data\UpdateInterface;

class UpdateValidatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Staging\Model\Update\UpdateValidator */
    private $model;

    /** @var ObjectManagerHelper */
    private $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $this->objectManagerHelper->getObject(
            \Magento\Staging\Model\Update\UpdateValidator::class
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage The Start Time of this Update cannot be changed. It's been already started.
     */
    public function testValidateUpdateStartedExecption()
    {
        $updateId = 1;
        $stagingData = [
            'update_id' => $updateId,
            'start_time' => '2017-02-02 02:02:02',
        ];

        $updateMock = $this->getMock(UpdateInterface::class);
        $this->model->validateUpdateStarted($updateMock, $stagingData);
    }

    public function testValidateUpdateStarted()
    {
        $updateId = 1;
        $stagingData = [
            'update_id' => $updateId,
            'start_time' => '2019-02-02 02:02:02',
        ];

        $updateMock = $this->getMock(UpdateInterface::class);
        $this->model->validateUpdateStarted($updateMock, $stagingData);
    }

    public function testValidateParams()
    {
        $params = [
            'stagingData' => [],
            'entityData' => []
        ];

        $this->model->validateParams($params);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage stagingData is required parameter.
     */
    public function testValidateWithInvalidParam()
    {
        $params = [
            'entityData' => []
        ];

        $this->model->validateParams($params);
    }
}
