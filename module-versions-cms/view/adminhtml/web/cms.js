/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    "jquery",
    "prototype",
    "mage/adminhtml/events"
], function (jQuery) {
    function dataChanged() {
        jQuery('#save_publish_button').removeClass('no-display').show();
        jQuery('#publish_button').hide();
    }
    jQuery('[data-role=cms-revision-form-changed]').on('change', dataChanged);
    varienGlobalEvents.attachEventHandler('tinymceChange', dataChanged);
});
