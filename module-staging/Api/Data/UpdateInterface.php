<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Api\Data;

/**
 * Update interface
 * @api
 */
interface UpdateInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /*
     * Id
     */
    const ID = 'id';

    /*
     * Start date time
     */
    const START_TIME = 'start_time';

    /*
     * Name
     */
    const NAME = 'name';

    /*
     * Description
     */
    const DESCRIPTION = 'description';

    /*
     * Id of next update (available if current update is temprary)
     */
    const ROLLBACK_ID = 'rollback_id';

    /*
     * Is it a single update or a campaign of multiple updates (update for multiple entities)
     */
    const IS_CAMPAIGN = 'is_campaign';

    /*
     * Is current update created by system to perform a rollback of temporary update
     */
    const IS_ROLLBACK = 'is_rollback';

    /**
     * End time of update. Not present in database and used to create rollback update
     */
    const END_TIME = 'end_time';

    /**
     * New update ID to which current update should be moved.
     * Saved to database only when start_time was changed and new update was created with this start_time.
     * Update with not empty moved_to is used only for synchronization of assigned entities.
     * All other actions should ignore such updates
     */
    const MOVED_TO = 'moved_to';

    /**
     * Retrieve update id
     *
     * @return int
     */
    public function getId();

    /**
     * Retrieve update start datetime
     *
     * @return string
     */
    public function getStartTime();

    /**
     * Retrieve update name
     *
     * @return string
     */
    public function getName();

    /**
     * Retrieve update description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Retrieve update rollback id
     *
     * @return int
     */
    public function getRollbackId();

    /**
     * Check if update is a campaign
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsCampaign();

    /**
     * Check if update is a rollback
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsRollback();

    /**
     * @return string
     */
    public function getEndTime();

    /**
     * Retrieve update id to which current update should be moved
     *
     * @return int|null
     */
    public function getMovedTo();

    /**
     * Retrieve update id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Set update start datetime
     *
     * @param string $time
     * @return $this
     */
    public function setStartTime($time);

    /**
     * Set update name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Set update description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Set next update id
     *
     * @param int $id
     * @return $this
     */
    public function setRollbackId($id);

    /**
     * Claim that update is a campaign
     *
     * @param string $isCampaign
     * @return $this
     */
    public function setIsCampaign($isCampaign);

    /**
     * Claim that update is a rollback
     *
     * @param bool $isRollback
     * @return $this
     */
    public function setIsRollback($isRollback);

    /**
     * Set update end time
     *
     * @param string $time
     * @return $this
     */
    public function setEndTime($time);

    /**
     * Set new update id to which current update should be moved
     *
     * @param int $id
     * @return $this
     */
    public function setMovedTo($id);

    /**
     * @return \Magento\Staging\Api\Data\UpdateExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \Magento\Staging\Api\Data\UpdateExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Magento\Staging\Api\Data\UpdateExtensionInterface $extensionAttributes);
}
