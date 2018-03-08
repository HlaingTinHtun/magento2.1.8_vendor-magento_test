<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TargetRule\Controller\Adminhtml\Targetrule;

class NewActionsHtml extends \Magento\TargetRule\Controller\Adminhtml\Targetrule
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->conditionsHtmlAction('actions');
    }
}
