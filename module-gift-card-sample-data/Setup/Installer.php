<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardSampleData\Setup;

use Magento\Framework\Setup;

class Installer implements Setup\SampleData\InstallerInterface
{
    /**
     * Setup class for category
     *
     * @var \Magento\CatalogSampleData\Model\Category
     */
    protected $categorySetup;

    /**
     * Setup class for product attributes
     *
     * @var \Magento\CatalogSampleData\Model\Attribute
     */
    protected $attributeSetup;

    /**
     * @var \Magento\ProductLinksSampleData\Model\ProductLink
     */
    protected $productLinkSetup;

    /**
     * @var \Magento\GiftCardSampleData\Model\Product
     */
    protected $product;

    /**
     * Setup class for css
     *
     * @var \Magento\ThemeSampleData\Model\Css
     */
    private $css;

    /**
     * @param \Magento\CatalogSampleData\Model\Category $categorySetup
     * @param \Magento\CatalogSampleData\Model\Attribute $attributeSetup
     * @param \Magento\GiftCardSampleData\Model\Product $product
     * @param \Magento\ProductLinksSampleData\Model\ProductLink $productLinkSetup
     * @param \Magento\ThemeSampleData\Model\Css $css
     */
    public function __construct(
        \Magento\CatalogSampleData\Model\Category $categorySetup,
        \Magento\CatalogSampleData\Model\Attribute $attributeSetup,
        \Magento\GiftCardSampleData\Model\Product $product,
        \Magento\ProductLinksSampleData\Model\ProductLink $productLinkSetup,
        \Magento\ThemeSampleData\Model\Css $css
    ) {
        $this->product = $product;
        $this->attributeSetup = $attributeSetup;
        $this->categorySetup = $categorySetup;
        $this->productLinkSetup = $productLinkSetup;
        $this->css = $css;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->attributeSetup->install(['Magento_GiftCardSampleData::fixtures/attributes.csv']);
        $this->categorySetup->install(['Magento_GiftCardSampleData::fixtures/categories.csv']);
        $this->product->install(
            ['Magento_GiftCardSampleData::fixtures/products_giftcard.csv'],
            ['Magento_GiftCardSampleData::fixtures/images_giftcard.csv']
        );
        $this->productLinkSetup->install(
            ['Magento_GiftCardSampleData::fixtures/related.csv'],
            [],
            ['Magento_GiftCardSampleData::fixtures/crossell.csv']
        );
        $this->css->install(['Magento_GiftCardSampleData::fixtures/styles.css' => 'styles.css']);
    }
}
