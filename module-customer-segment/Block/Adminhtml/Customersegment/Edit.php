<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Block\Adminhtml\Customersegment;

/**
 * Edit form for customer segment configuration
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Refresh Segment Data" button
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_customersegment';
        $this->_blockGroup = 'Magento_CustomerSegment';

        parent::_construct();

        /** @var $segment \Magento\CustomerSegment\Model\Segment */
        $segment = $this->_coreRegistry->registry('current_customer_segment');
        if ($segment && $segment->getId()) {
            $this->buttonList->add(
                'match_customers',
                [
                    'label' => __('Refresh Segment Data'),
                    'onclick' => 'setLocation(\'' . $this->getMatchUrl() . '\')'
                ],
                -1
            );
        }

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class' => 'save',
                'label' => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            3
        );
    }

    /**
     * Get url for run segment customers matching
     *
     * @return string
     */
    public function getMatchUrl()
    {
        $segment = $this->_coreRegistry->registry('current_customer_segment');
        return $this->getUrl('*/*/match', ['id' => $segment->getId()]);
    }

    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $segment = $this->_coreRegistry->registry('current_customer_segment');
        if ($segment->getSegmentId()) {
            return __("Edit Segment '%1'", $this->escapeHtml($segment->getName()));
        } else {
            return __('New Segment');
        }
    }
}
