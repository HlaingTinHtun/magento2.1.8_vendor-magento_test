<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CmsStaging\Test\Unit\Model;

use Magento\CmsStaging\Model\PageApplier;
use Magento\Framework\Indexer\CacheContext;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class PageApplierTest extends \PHPUnit_Framework_TestCase
{
    /** @var CacheContext|\PHPUnit_Framework_MockObject_MockObject */
    private $cacheContext;

    /** @var PageApplier|\PHPUnit_Framework_MockObject_MockObject */
    private $stagingApplier;
    
    public function setUp()
    {
        $this->cacheContext = $this->getMockBuilder(\Magento\Framework\Indexer\CacheContext::class)
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new ObjectManager($this);

        $this->stagingApplier = $objectManager->getObject(PageApplier::class, [
            'cacheContext' => $this->cacheContext,
        ]);
    }

    /**
     * @return array
     */
    public function getEntityIds()
    {
        return [
            [[1, 2, 3]],
            [[]],
        ];
    }

    /**
     * @dataProvider getEntityIds
     */
    public function testRegisterCmsCacheTag($entityIds)
    {
        if (!empty($entityIds)) {
            $this->cacheContext->expects($this->once())
                ->method('registerEntities')
                ->with(\Magento\Cms\Model\Page::CACHE_TAG, $entityIds);
        }

        $this->stagingApplier->execute($entityIds);
    }
}
