<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogRuleStaging\Test\Unit\Model\Plugin;

use Magento\CatalogRuleStaging\Model\Plugin\ConditionFieldsetIdResolver;

class ConditionFieldsetIdResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConditionFieldsetIdResolver
     */
    protected $plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataPoolMock;

    protected function setUp()
    {
        $this->metadataPoolMock = $this->getMock('Magento\Framework\EntityManager\MetadataPool', [], [], '', false);
        $this->plugin = new ConditionFieldsetIdResolver(
            $this->metadataPoolMock
        );
    }

    public function testAroundGetConditionsFieldSetId()
    {
        $result = 'result';
        $formName = 'form_name';
        $ruleMock = $this->getMock('Magento\CatalogRule\Model\Rule', [], [], '', false);
        $entityMetadataMock = $this->getMock('\Magento\Framework\EntityManager\EntityMetadata', [], [], '', false);
        $proceed = function () use ($result) {
            return $result;
        };
        $this->metadataPoolMock
            ->expects($this->once())
            ->method('getMetadata')
            ->with(\Magento\CatalogRule\Api\Data\RuleInterface::class)
            ->willReturn($entityMetadataMock);
        $entityMetadataMock->expects($this->once())->method('getLinkField')->willReturn('rule_id');
        $ruleMock->expects($this->once())->method('getData')->with('rule_id')->willReturn(1);
        $this->assertEquals(
            $formName . 'rule_conditions_fieldset_1',
            $this->plugin->aroundGetConditionsFieldSetId($ruleMock, $proceed, $formName)
        );
    }
}
