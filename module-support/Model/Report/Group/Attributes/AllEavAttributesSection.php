<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Model\Report\Group\Attributes;

/**
 * All Eav Attributes section of Attributes report group
 */
class AllEavAttributesSection extends AbstractAttributesSection
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $attributeCollection = $this->getAttributesCollection();
        return [
            (string)__('All Eav Attributes') => $this->generateSectionData($attributeCollection)
        ];
    }
}
