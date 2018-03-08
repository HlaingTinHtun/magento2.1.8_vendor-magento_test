<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Model\Entity\Update\Action;

use Magento\Staging\Model\Entity\Update\Action\ActionInterface;

interface TransactionExecutorInterface extends ActionInterface
{
    /**
     * @param ActionInterface $action
     * @return mixed
     */
    public function setAction(ActionInterface $action);
}
