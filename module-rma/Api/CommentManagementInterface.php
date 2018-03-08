<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Rma\Api;

/**
 * Interface CommentRepositoryInterface
 * @api
 */
interface CommentManagementInterface
{
    /**
     * Add comment
     *
     * @param \Magento\Rma\Api\Data\CommentInterface $data
     * @return bool
     * @throws \Exception
     */
    public function addComment(\Magento\Rma\Api\Data\CommentInterface $data);

    /**
     * Comments list
     *
     * @param int $id
     * @return \Magento\Rma\Api\Data\CommentSearchResultInterface
     */
    public function commentsList($id);
}
