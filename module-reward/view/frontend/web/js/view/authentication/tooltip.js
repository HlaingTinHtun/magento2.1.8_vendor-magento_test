/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true*/
/*global define*/
define(
    [
        'uiComponent'
    ],
    function (Component) {
        return Component.extend({
            defaults: {
                template: 'Magento_Reward/authentication/tooltip'
            },
            isAvailable: window.checkoutConfig.authentication.reward.isAvailable,
            tooltipLearnMoreUrl: window.checkoutConfig.authentication.reward.tooltipLearnMoreUrl,
            tooltipMessage: window.checkoutConfig.authentication.reward.tooltipMessage
        });
    }
);
