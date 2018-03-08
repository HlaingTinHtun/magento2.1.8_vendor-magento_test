<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogRuleStaging\Setup;

/**
 * @codeCoverageIgnore
 */
class CatalogRuleSetup
{
    /**
     * @var \Magento\Staging\Api\UpdateRepositoryInterface
     */
    private $updateRepository;

    /**
     * @var \Magento\Staging\Api\Data\UpdateInterfaceFactory
     */
    private $updateFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Staging\Model\VersionManagerFactory
     */
    private $versionManagerFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @param \Magento\Staging\Api\UpdateRepositoryInterface $updateRepository
     * @param \Magento\Staging\Api\Data\UpdateInterfaceFactory $updateFactory
     * @param \Magento\CatalogRule\Model\RuleFactory $ruleFactory
     * @param \Magento\Framework\App\State $state
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Staging\Model\VersionManagerFactory $versionManagerFactory
     */
    public function __construct(
        \Magento\Staging\Api\UpdateRepositoryInterface $updateRepository,
        \Magento\Staging\Api\Data\UpdateInterfaceFactory $updateFactory,
        \Magento\CatalogRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\App\State $state,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Staging\Model\VersionManagerFactory $versionManagerFactory
    ) {
        $this->updateRepository = $updateRepository;
        $this->updateFactory = $updateFactory;
        $this->ruleFactory = $ruleFactory;
        $this->logger = $logger;
        $this->versionManagerFactory = $versionManagerFactory;
        $this->state = $state;
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    public function migrateRules($setup)
    {
        // Emulate area for rules migration
        $this->state->emulateAreaCode(
            \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
            [$this, 'updateRules'],
            [$setup]
        );
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    public function updateRules($setup)
    {
        $versionManager = $this->versionManagerFactory->create();
        $catalogRuleEntityTable = $setup->getTable('catalogrule');

        $select = $setup->getConnection()->select()->from(
            $catalogRuleEntityTable,
            ['row_id', 'rule_id', 'name', 'from_date', 'to_date']
        );
        $rules = $setup->getConnection()->fetchAll($select);

        foreach ($rules as $rule) {
            try {
                // Set current update version
                $versionManager->setCurrentVersionId(
                    $this->createUpdateForRule($rule)->getId()
                );

                /** @var \Magento\CatalogRule\Model\Rule $ruleModel */
                $ruleModel = $this->ruleFactory->create();
                $ruleModel->load($rule['rule_id']);
                $ruleModel->unsRowId();
                $ruleModel->setIsActive(1);

                // Set is_active = false for rule entity
                $setup->getConnection()->update(
                    $catalogRuleEntityTable,
                    ['is_active' => 0],
                    ['row_id = ?' => $rule['row_id']]
                );

                // Save staging update for rule
                $ruleModel->save();
            } catch (\Magento\Framework\Exception\ValidatorException $exception) {
                // Set is_active = false for rule with dates in past
                $setup->getConnection()->update(
                    $catalogRuleEntityTable,
                    ['is_active' => 0],
                    ['row_id = ?' => $rule['row_id']]
                );
                continue;
            } catch (\Exception $exception) {
                $this->logger->critical($exception);
            }
        }
    }

    /**
     * @param array $rule
     * @return \Magento\Staging\Api\Data\UpdateInterface
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    private function createUpdateForRule($rule)
    {
        /** @var \Magento\Staging\Api\Data\UpdateInterface $update */
        $update = $this->updateFactory->create();
        $update->setName($rule['name']);

        $fromDate = $rule['from_date'] ? $rule['from_date'] : 'now';
        $date = new \DateTime($fromDate, new \DateTimeZone('UTC'));
        $update->setStartTime($date->format('Y-m-d 00:00:00'));

        $currentDateTime = new \DateTime('now', new \DateTimeZone('UTC'));
        if (strtotime($update->getStartTime()) < $currentDateTime->getTimestamp()) {
            $currentDateTime->modify('+1 minutes');
            $update->setStartTime($currentDateTime->format('Y-m-d H:i:s'));
        }

        if ($rule['to_date']) {
            $date = new \DateTime($rule['to_date'], new \DateTimeZone('UTC'));
            $update->setEndTime($date->format('Y-m-d 23:59:59'));
        }

        if ($update->getEndTime() && strtotime($update->getEndTime()) <= $currentDateTime->getTimestamp()) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('Future Update End Time cannot be equal or earlier than Start Time.')
            );
        }

        $update->setIsCampaign(false);
        $this->updateRepository->save($update);
        return $update;
    }
}
