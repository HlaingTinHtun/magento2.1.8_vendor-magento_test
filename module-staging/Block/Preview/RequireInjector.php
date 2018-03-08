<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Block\Preview;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Staging\Model\VersionManager;

/**
 * Class RequireInjector
 */
class RequireInjector extends Template
{
    const INJECTIONS_LIST = 'requireInjectionsList';
    const MODULE_NAME = 'requireModuleName';

    /**
     * @var VersionManager
     */
    private $versionManager;

    /**
     * @param Context $context
     * @param VersionManager $versionManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        VersionManager $versionManager,
        array $data = []
    ) {
        $this->versionManager = $versionManager;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->versionManager->isPreviewVersion()
            && $this->getInjectionsList() != null
            && $this->getRequireModuleName() != null
        ) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Get module name
     *
     * @return string|null
     */
    public function getRequireModuleName()
    {
        return $this->getData(self::MODULE_NAME);
    }

    /**
     * Get injections list
     *
     * @return array|null
     */
    public function getInjectionsList()
    {
        return $this->getData(self::INJECTIONS_LIST);
    }
}
