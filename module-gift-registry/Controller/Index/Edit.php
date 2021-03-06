<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Controller\Index;

use Magento\Framework\Exception\LocalizedException;

class Edit extends \Magento\GiftRegistry\Controller\Index
{
    /**
     * Select gift registry type action
     *
     * @return void
     */
    public function execute()
    {
        $typeId = $this->getRequest()->getParam('type_id');
        $entityId = $this->getRequest()->getParam('entity_id');
        try {
            if (!$typeId) {
                if (!$entityId) {
                    $this->_redirect('*/*/');
                    return;
                } else {
                    // editing existing entity
                    /* @var $model \Magento\GiftRegistry\Model\Entity */
                    $model = $this->_initEntity('entity_id');
                }
            }

            if ($typeId && !$entityId) {
                // creating new entity
                /* @var $model \Magento\GiftRegistry\Model\Entity */
                $model = $this->_objectManager->get('Magento\GiftRegistry\Model\Entity');
                if ($model->setTypeById($typeId) === false) {
                    throw new LocalizedException(__('Please correct the gift registry.'));
                }
            }

            $this->_coreRegistry->register('magento_giftregistry_entity', $model);
            $this->_coreRegistry->register('magento_giftregistry_address', $model->exportAddress());

            $this->_view->loadLayout();

            if ($model->getId()) {
                $pageTitle = __('Edit Gift Registry');
            } else {
                $pageTitle = __('Create Gift Registry');
            }
            $this->_view->getPage()->getConfig()->getTitle()->set($pageTitle);
            $this->_view->renderLayout();
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/*/');
        }
    }
}
