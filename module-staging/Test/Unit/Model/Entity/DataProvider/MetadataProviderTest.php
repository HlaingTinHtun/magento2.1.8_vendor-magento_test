<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Model\Entity\DataProvider;

use Magento\Staging\Model\Entity\DataProvider\MetadataProvider;

class MetadataProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $updateRepositoryMock;

    /**
     * @var MetadataProvider
     */
    private $provider;

    protected function setUp()
    {
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->updateRepositoryMock = $this->getMock('Magento\Staging\Api\UpdateRepositoryInterface');

        $this->provider = new MetadataProvider(
            $this->requestMock,
            $this->updateRepositoryMock,
            'entity_id'
        );
    }

    public function testGetMetadataForExistingUpdate()
    {
        $updateId = 1;
        $isCampaign = true;
        $updateName = 'Update Name';
        $this->requestMock->expects($this->any())->method('getParam')->willReturn(1);
        $updateMock = $this->getMock('Magento\Staging\Api\Data\UpdateInterface');
        $updateMock->expects($this->any())->method('getName')->willReturn($updateName);
        $updateMock->expects($this->any())->method('getIsCampaign')->willReturn($isCampaign);

        $this->updateRepositoryMock->expects($this->any())->method('get')->with($updateId)->willReturn($updateMock);

        $expectedResult['staging']['children'] = [
            'staging_save' => [
                'children' => [
                    'staging_save_mode' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'description' => __('Edit Existing Update') . ' ' . $updateName,
                                ],
                            ],
                        ],
                    ],
                    'staging_save_name' => [
                        'arguments' => [
                            'data' => [
                                'config' => ['disabled' => $isCampaign],
                            ],
                        ],
                    ],
                    'staging_save_description' => [
                        'arguments' => [
                            'data' => [
                                'config' => ['disabled' => $isCampaign],
                            ],
                        ],
                    ],
                    'staging_save_start_date' => [
                        'arguments' => [
                            'data' => [
                                'config' => ['disabled' => $isCampaign],
                            ],
                        ],
                    ],
                    'staging_save_end_time' => [
                        'arguments' => [
                            'data' => [
                                'config' => ['disabled' => $isCampaign],
                            ],
                        ],
                    ],
                ],
            ],
            'staging_select' => [
                'children' => [
                    'staging_select_mode' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'description' => __('Assign to Another Campaign'),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expectedResult, $this->provider->getMetadata());
    }
}
