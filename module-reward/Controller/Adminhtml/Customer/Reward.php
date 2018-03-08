<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Reward admin customer controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Controller\Adminhtml\Customer;

abstract class Reward extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = \Magento\Reward\Helper\Data::XML_PATH_PERMISSION_BALANCE;

    /**
     * Check if module functionality enabled
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->_objectManager->get(
            'Magento\Reward\Helper\Data'
        )->isEnabled() && $request->getActionName() != 'noroute'
        ) {
            $this->_forward('noroute');
        }
        return parent::dispatch($request);
    }
}
