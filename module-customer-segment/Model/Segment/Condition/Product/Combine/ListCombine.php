<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Product\Combine;

use Magento\Customer\Model\Customer;
use Zend_Db_Expr;

/**
 * Shopping cart/wishlist items condition
 */
class ListCombine extends \Magento\CustomerSegment\Model\Condition\Combine\AbstractCombine
{
    /**
     * Flag of using condition combine (for conditions of Product_Attribute)
     *
     * @var bool
     */
    protected $_combineProductCondition = true;

    const WISHLIST = 'wishlist';

    const CART = 'shopping_cart';

    /**
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote
     */
    private $quoteResource;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param \Magento\CustomerSegment\Model\ResourceModel\Segment $resourceSegment
     * @param \Magento\Quote\Model\ResourceModel\Quote $quoteResource
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        \Magento\CustomerSegment\Model\ResourceModel\Segment $resourceSegment,
        \Magento\Quote\Model\ResourceModel\Quote $quoteResource,
        array $data = []
    ) {
        parent::__construct($context, $conditionFactory, $resourceSegment, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Product\Combine\ListCombine');
        $this->setValue(self::CART);
        $this->quoteResource = $quoteResource;
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return string[]
     */
    public function getMatchedEvents()
    {
        $events = [];
        switch ($this->getValue()) {
            case self::WISHLIST:
                $events = ['wishlist_items_renewed'];
                break;
            default:
                $events = ['checkout_cart_save_after'];
                break;
        }
        return $events;
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return $this->_conditionFactory->create(
            'Product\Combine'
        )->setDateConditions(
            true
        )->getNewChildSelectOptions();
    }

    /**
     * Initialize value select options
     *
     * @return $this
     */
    public function loadValueOptions()
    {
        $this->setValueOption([self::CART => __('Shopping Cart'), self::WISHLIST => __('Wish List')]);
        return $this;
    }

    /**
     * Set rule instance
     *
     * Modify value_option array if needed
     *
     * @param \Magento\Rule\Model\AbstractModel $rule
     * @return $this
     */
    public function setRule($rule)
    {
        $this->setData('rule', $rule);
        if ($rule instanceof \Magento\CustomerSegment\Model\Segment && $rule->getApplyTo() !== null) {
            $option = $this->loadValueOptions()->getValueOption();
            $applyTo = $rule->getApplyTo();
            if (\Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS == $applyTo) {
                unset($option[self::WISHLIST]);
            } elseif (\Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS_AND_REGISTERED == $applyTo) {
                $option[self::CART] .= '*';
            }
            $this->setValueOption($option);
        }
        return $this;
    }

    /**
     * Get input type for attribute value.
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Prepare operator select options
     *
     * @return $this
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setOperatorOption(['==' => __('found'), '!=' => __('not found')]);
        return $this;
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml() . __(
            'If Product is %1 in the %2 with %3 of these Conditions match:',
            $this->getOperatorElementHtml(),
            $this->getValueElementHtml(),
            $this->getAggregatorElement()->getHtml()
        ) . $this->getRemoveLinkHtml();
    }

    /**
     * Build query for matching shopping cart/wishlist items
     *
     * @param Customer|Zend_Db_Expr $customer
     * @param int|Zend_Db_Expr $website
     * @param bool $isFiltered
     * @return \Magento\Framework\DB\Select
     */
    protected function _prepareConditionsSql($customer, $website, $isFiltered = true)
    {
        $select = $this->getResource()->createSelect();

        switch ($this->getValue()) {
            case self::WISHLIST:
                $select->from(
                    ['item' => $this->getResource()->getTable('wishlist_item')],
                    [new \Zend_Db_Expr(1)]
                );
                $conditions = "item.wishlist_id = list.wishlist_id";
                $select->joinInner(['list' => $this->getResource()->getTable('wishlist')], $conditions, []);
                $this->_limitByStoreWebsite($select, $website, 'item.store_id');
                if ($isFiltered) {
                    $select->where($this->_createCustomerFilter($customer, 'list.customer_id'));
                    $select->limit(1);
                }
                break;
            default:
                    $select->from(
                        ['item' => $this->getResource()->getTable('quote_item')],
                        [new \Zend_Db_Expr(1)]
                    );
                $conditions = "item.quote_id = list.entity_id";
                $select->joinInner(
                    ['list' => $this->getResource()->getTable('quote')],
                    $conditions,
                    []
                );
                $this->_limitByStoreWebsite($select, $website, 'list.store_id');
                $select->where('list.is_active = ?', new \Zend_Db_Expr(1));
                if ($customer) {
                    // Leave ability to check this condition not only by customer_id but also by quote_id
                    $select->where('list.customer_id = :customer_id OR list.entity_id = :quote_id');
                } elseif ($isFiltered) {
                    $select->where($this->_createCustomerFilter($customer, 'list.customer_id'));
                    $select->limit(1);
                }
                break;
        }
        return $select;
    }

    /**
     * Check if validation should be strict
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    protected function _getRequiredValidation()
    {
        return $this->getOperator() == '==';
    }

    /**
     * Get field names map for subfilter conditions
     *
     * @return array
     */
    protected function _getSubfilterMap()
    {
        switch ($this->getValue()) {
            case self::WISHLIST:
                $dateField = 'item.added_at';
                break;

            default:
                $dateField = 'item.created_at';
                break;
        }

        return ['product' => 'item.product_id', 'date' => $dateField];
    }

    /**
     * @param int $customer
     * @param int $websiteId
     * @param array $params
     * @return bool
     */
    public function isSatisfiedBy($customer, $websiteId, $params)
    {
        $result = false;
        switch ($this->getValue()) {
            case self::WISHLIST:
                if (!$customer) {
                    return $this->getResource()->createSelect()->from(
                        ['' => new \Zend_Db_Expr('dual')],
                        [new \Zend_Db_Expr(0)]
                    );
                }
                // check wishlist related conditions directly
                $select = $this->getConditionsSql($customer, $websiteId);
                if (isset($select)) {
                    $matchedParams = $this->matchParameters($select, $params);
                    $result = $this->getResource()->getConnection()->fetchOne($select, $matchedParams);
                }
                return $result > 0;
                break;
            case self::CART:
                // retrieve appropriate store IDs
                $storeIds = $this->getStoreByWebsite($websiteId);
                // select information about all quote items related to given customer
                $select = $this->getResource()->createSelect();
                $select->from(
                    ['item' => $this->getResource()->getTable('quote_item')],
                    '*'
                )->joinInner(
                    ['quote' => $this->getResource()->getTable('quote')],
                    'item.quote_id = quote.entity_id',
                    []
                )->where(
                    'quote.is_active = 1'
                )->where(
                    'quote.store_id IN (?)',
                    $storeIds
                )->where('quote.customer_id = ?', $customer);
                $relatedQuoteItems = $this->quoteResource->getConnection()->fetchAll($select);

                $mustBeFoundInCart = $this->_getRequiredValidation();
                foreach ($relatedQuoteItems as $relatedQuoteItem) {
                    $result = $this->isSatisfiedByQuoteItem($relatedQuoteItem, $customer, $websiteId);
                    if ($result) {
                        if ($mustBeFoundInCart) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                }
                return !$mustBeFoundInCart;
                break;
        }
    }

    /**
     * Check if given quote item matches required criteria
     *
     * @param array $quoteItem
     * @param int $customer
     * @param int $websiteId
     * @return bool
     */
    protected function isSatisfiedByQuoteItem(array $quoteItem, $customer, $websiteId)
    {
        $checkAll = $this->getAggregator() == 'all';
        foreach ($this->getConditions() as $condition) {
            $result = $condition->isSatisfiedBy(
                $customer,
                $websiteId,
                [
                    'quote_item' => $quoteItem,
                    'validation_required' => $this->_getRequiredValidation(),
                ]
            );
            if (!$result && $checkAll) {
                return false;
            }
            if ($result && !$checkAll) {
                return true;
            }
        }
        return $checkAll;
    }

    /**
     * Get assigned to website store ids
     *
     * @TODO move this method to rule module
     * @param int $websiteId
     * @return array
     */
    protected function getStoreByWebsite($websiteId)
    {
        $storeTable = $this->getResource()->getTable('store');
        $storeSelect = $this->getResource()->createSelect()->from($storeTable, ['store_id'])->where(
            'website_id=?',
            $websiteId
        );
        $data = $this->getResource()->getConnection()->fetchCol($storeSelect);
        return $data;
    }

    /**
     * Retrieve the list of customer IDs that match corresponding conditions
     *
     * @param int $websiteId
     * @return array
     */
    public function getSatisfiedIds($websiteId)
    {
        $checkAll = $this->getAggregator() == 'all';
        $mustBeFoundInCart = $this->_getRequiredValidation();
        $connection = $this->getResource()->getConnection();

        $count = 0;
        switch ($this->getValue()) {
            case self::WISHLIST:
                $customerSelect = $this->getResource()->createSelect();
                $table = $this->getResource()->getTable('customer_entity');
                $customerSelect->from(['root' => $table], ['entity_id']);
                $customerSelect->where('root.website_id =?', $websiteId);
                $sqlOperator = $this->getIsRequired() ? '=' : '<>';
                $isnull = $connection->getCheckSql($this->getConditionsSql(null, $websiteId, true), 1, 0);
                $condition = "({$isnull} {$sqlOperator} 1)";
                //check if condition satisfy customers
                $customerSelect->where($condition);
                $result = $this->getResource()->getConnection()->fetchCol($customerSelect);
                return $result;
                break;
            default:
                $storeIds = $this->getStoreByWebsite($websiteId);
                $quoteIds = [];
                $required = $this->_getRequiredValidation();
                foreach ($this->getConditions() as $condition) {
                    if ($mustBeFoundInCart) {
                        // quote IDs that match child condition
                        $matchedQuoteIds = $condition->getSatisfiedIds($websiteId, $required);
                    } else {
                        // quote IDs that do not match child condition
                        $matchedQuoteIds = $this->getQuoteIds(
                            $storeIds,
                            $condition->getSatisfiedIds($websiteId, $required)
                        );
                    }
                    if ($checkAll) {
                        if ($count > 0) {
                            $quoteIds = array_intersect($matchedQuoteIds, $quoteIds);
                        } else {
                            $quoteIds = $matchedQuoteIds;
                        }
                        if (empty($quoteIds)) {
                            return [];
                        }
                        $count++;
                    } else {
                        $quoteIds = array_merge($matchedQuoteIds, $quoteIds);
                    }
                }
                $result = $this->getSatisfiedCustomerIds(array_unique($quoteIds));
                break;
        }
        return $result;
    }

    /**
     * Retrieve the list of quote IDs from given stores
     *
     * @param array $storeIds target store IDs
     * @param array $excludedIds quote IDs that do not match criteria
     * @return array
     */
    protected function getQuoteIds(array $storeIds, array $excludedIds)
    {
        $connection = $this->quoteResource->getConnection();
        $select = $connection->select();
        $select->from(['quote' => $this->quoteResource->getTable('quote')], ['entity_id'])
            ->where('quote.store_id IN(?)', $storeIds)
            ->where('quote.is_active = 1');
        if (!empty($excludedIds)) {
            $select->where('quote.entity_id NOT IN(?)', $excludedIds);
        }

        return $connection->fetchCol($select);
    }

    /**
     * Get customer IDs by quote IDs
     *
     * @param int $quoteIds
     * @return array
     */
    protected function getSatisfiedCustomerIds($quoteIds)
    {
        $result = [];
        if (!empty($quoteIds)) {
            $quoteIdCondition = 'entity_id IN(?)';
            $select = $this->quoteResource->getConnection()->select()
                ->from(
                    [$this->quoteResource->getTable('quote')],
                    ['customer_id']
                )->where(
                    'customer_id IS NOT NULL'
                )->where(
                    'is_active = 1'
                )->where(
                    $quoteIdCondition,
                    $quoteIds
                );
            $result = $this->quoteResource->getConnection()->fetchCol($select);
        }
        return array_unique($result);
    }
}
