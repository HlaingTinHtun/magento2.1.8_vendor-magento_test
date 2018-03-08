<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\LayoutFactory;

class AddProduct extends \Magento\Backend\App\Action
{
    /**
     * @var LayoutFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param LayoutFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        LayoutFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Layout $resultPage */
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
