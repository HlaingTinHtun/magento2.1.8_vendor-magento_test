<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Elasticsearch\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Eav\Model\Config;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Elasticsearch index resource model.
 */
class Index extends \Magento\AdvancedSearch\Model\ResourceModel\Index
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * Index constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param MetadataPool $metadataPool
     * @param ProductRepositoryInterface $productRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Config $eavConfig
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        MetadataPool $metadataPool,
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        Config $eavConfig,
        $connectionName = null
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->eavConfig = $eavConfig;
        parent::__construct($context, $storeManager, $metadataPool, $connectionName);
    }

    /**
     * Retrieve all attributes for given product ids.
     *
     * @param int $productId
     * @param array $indexData
     * @return array
     */
    public function getFullProductIndexData($productId, $indexData)
    {
        $productAttributes = [];
        $attributeCodes = $this->eavConfig->getEntityAttributeCodes(ProductAttributeInterface::ENTITY_TYPE_CODE);
        $product = $this->productRepository->getById($productId);
        foreach ($attributeCodes as $attributeCode) {
            $value = $product->getData($attributeCode);
            $attribute = $this->eavConfig->getAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                $attributeCode
            );
            $frontendInput = $attribute->getFrontendInput();
            if (in_array($attribute->getAttributeId(), array_keys($indexData))) {
                if (is_array($indexData[$attribute->getAttributeId()])) {
                    if (isset($indexData[$attribute->getAttributeId()][$productId])) {
                        $value = $indexData[$attribute->getAttributeId()][$productId];
                    } else {
                        $value = array_values($indexData[$attribute->getAttributeId()]);
                    }
                } else {
                    $value = $indexData[$attribute->getAttributeId()];
                }
            }

            if ($value) {
                $productAttributes[$attributeCode] = $value;

                if ($frontendInput == 'select') {
                    $valueLabel = $this->getOptionsLabel($attribute, $value);
                    $productAttributes[$attributeCode . '_value'] = $valueLabel;
                }
            }
        }

        return $productAttributes;
    }

    /**
     * Prepare full category index data for products.
     *
     * @param int $storeId
     * @param null|array $productIds
     * @return array
     */
    public function getFullCategoryProductIndexData($storeId = null, $productIds = null)
    {
        $categoryPositions = $this->getCategoryProductIndexData($storeId, $productIds);
        $categoryData = [];

        foreach ($categoryPositions as $productId => $positions) {
            foreach ($positions as $categoryId => $position) {
                $category = $this->categoryRepository->get($categoryId, $storeId);
                $categoryName = $category->getName();
                $categoryData[$productId][] = [
                    'id' => $categoryId,
                    'name' => $categoryName,
                    'position' => $position
                ];
            }
        }

        return $categoryData;
    }

    /**
     * Returns attribute options label as one string.
     *
     * Retrieve attribute selected options label as string concatenated by space.
     * Used to return value for dropdown attribute '_value' index field.
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @param mixed $value
     * @return string
     */
    private function getOptionsLabel(
        \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute,
        $value
    ) {
        $attributeValue = (array) $value;
        $valueLabel = '';

        foreach ($attribute->getOptions() as $option) {
            if (in_array($option->getValue(), $attributeValue, true)) {
                $valueLabel .= (strlen($valueLabel) ? ' ' : '') . $option->getLabel();
            }
        }

        return $valueLabel;
    }
}
