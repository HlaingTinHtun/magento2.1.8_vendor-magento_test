/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'uiElement',
        'underscore'
    ],
    function(uiElement, _) {
        "use strict";

        var giftWrappingConfig = window.giftOptionsConfig ?
            window.giftOptionsConfig.giftWrapping :
            window.checkoutConfig.giftWrapping;

        var provider = uiElement();

        return function(itemId) {
            return {
                itemId: itemId,
                observables: {},
                initObservable: function(node, key) {
                    if (node && !this.observables.hasOwnProperty(node)) {
                        this.observables[node] = [];
                    }
                    if (key && this.observables[node].indexOf(key) == -1) {
                        this.observables[node].push(key);
                        provider.observe(this.getUniqueKey(node, key));
                    }
                },
                getUniqueKey: function(node, key) {
                    return node + '-' + key;
                },
                getObservable: function(node, key) {
                    this.initObservable(node, key);
                    return provider[this.getUniqueKey(node, key)];
                },
                unsetObservable: function(node, id) {
                    var self = this;

                    if (!(node || this.observables.hasOwnProperty(node))) {
                        return false;
                    }
                    if (id) {
                        provider[this.getUniqueKey(node, id)](false);
                        return true;
                    }
                    _.each(this.observables[node], function(key) {
                        provider[self.getUniqueKey(node, key)](false);
                    });
                },
                isHighlight: function(itemId, key) {
                    return this.getObservable('isHighlight-' + itemId, key);
                },
                setActiveItem: function(id) {
                    this.unsetObservable('isHighlight-' + this.itemId);
                    this.getObservable('isHighlight-' + this.itemId, id)(true);
                    this.getObservable('activeWrapping', this.itemId)(id);
                },
                uncheckWrapping: function () {
                    var activeWrappingId = this.getObservable('activeWrapping', this.itemId)();

                    this.getObservable('isHighlight-' + this.itemId, activeWrappingId)(false);
                    this.getObservable('activeWrapping', this.itemId)(null);
                },

                /**
                 * Retrieve gift wrapping items related to current scope
                 *
                 * @returns {Array}
                 */
                getWrappingItems: function () {
                    var cartItemId = this.itemId;

                    return _.map(giftWrappingConfig.designsInfo, function (item, id) {
                        var cartItemsConfig = giftWrappingConfig.itemsInfo || {},
                            price = 0,
                            priceExclTax = 0,
                            priceInclTax = 0,
                            cartItemConfig = null;

                        if (cartItemId !== 'orderLevel' &&
                            cartItemsConfig[cartItemId] &&
                            (cartItemsConfig[cartItemId].hasOwnProperty('price') ||
                                cartItemsConfig[cartItemId].hasOwnProperty('price_excl_tax') ||
                                cartItemsConfig[cartItemId].hasOwnProperty('price_incl_tax'))
                        ) {
                            // use product level configuration if it is available
                            cartItemConfig = cartItemsConfig[cartItemId];
                            price = cartItemConfig.price;
                            priceExclTax = cartItemConfig.hasOwnProperty('price_excl_tax') ?
                                cartItemConfig['price_excl_tax'] :
                                null;
                            priceInclTax = cartItemConfig.hasOwnProperty('price_incl_tax') ?
                                cartItemConfig['price_incl_tax'] :
                                null;
                        } else {
                            price = item.price;
                            priceExclTax = item.hasOwnProperty('price_excl_tax') ? item['price_excl_tax'] : null;
                            priceInclTax = item.hasOwnProperty('price_incl_tax') ? item['price_incl_tax'] : null;
                        }

                        return {
                            'id': id,
                            'label': item.label,
                            'path': item.path,
                            'price': price,
                            'priceExclTax': priceExclTax,
                            'priceInclTax': priceInclTax
                        };
                    });
                },
                isWrappingAvailable: function() {
                    // itemId represent gift wrapping level: 'orderLevel' constant or cart item ID
                    if (this.itemId == 'orderLevel') {
                        return this.getConfigValue('allowForOrder')
                            && (this.getWrappingItems().length > 0
                                || this.getConfigValue('isAllowGiftReceipt')
                                || this.getConfigValue('isAllowPrintedCard')
                            );
                    }

                    // gift wrapping product configuration must override system configuration
                    var isWrappingEnabled = (giftWrappingConfig.itemsInfo && giftWrappingConfig.itemsInfo[this.itemId])
                        ? giftWrappingConfig.itemsInfo[this.itemId]['is_available']
                        : this.getConfigValue('allowForItems');

                    return isWrappingEnabled && this.getWrappingItems().length > 0;
                },
                getActiveWrappingInfo: function() {
                    var self = this;

                    return _.find(this.getWrappingItems(), function(item) {
                        return item.id == self.getObservable('activeWrapping', self.itemId)();
                    });
                },
                getWrappingById: function(id) {
                    return _.find(this.getWrappingItems(), function(item) {
                        return item.id == id;
                    });
                },
                getConfigValue: function(key) {
                    return giftWrappingConfig[key];
                },
                getPriceFormat: function() {
                    return window.giftOptionsConfig.priceFormat;
                },
                getPrintedCardPrice: function() {
                    return giftWrappingConfig.cardInfo.hasOwnProperty('price') ?
                        giftWrappingConfig.cardInfo.price
                        : '';
                },
                getPrintedCardPriceWithTax: function() {
                    return giftWrappingConfig.cardInfo.hasOwnProperty('price_incl_tax') ?
                        giftWrappingConfig.cardInfo.price_incl_tax
                        : giftWrappingConfig.cardInfo.price;
                },
                getPrintedCardPriceWithoutTax: function() {
                    return giftWrappingConfig.cardInfo.hasOwnProperty('price_excl_tax') ?
                        giftWrappingConfig.cardInfo.price_excl_tax
                        : giftWrappingConfig.cardInfo.price;
                },
                getSubmitParams: function(remove) {
                    return {
                        wrappingId: remove ? null : this.getObservable('activeWrapping', this.itemId)(),
                        wrappingAddPrintedCard: remove && this.itemId == 'orderLevel'
                            ? false : this.getObservable('wrapping-' + this.itemId, 'printedCard')(),
                        wrappingAllowGiftReceipt: remove && this.itemId == 'orderLevel'
                            ? false : this.getObservable('wrapping-' + this.itemId, 'giftReceipt')()
                    };
                },
                getAppliedWrappingId: function() {
                    var levelType = this.itemId == 'orderLevel' ? 'orderLevel' : 'itemLevel';

                    if (!giftWrappingConfig.appliedWrapping.hasOwnProperty(levelType)) {
                        return null;
                    }
                    var appliedWrapping = giftWrappingConfig.appliedWrapping[levelType];

                    if (levelType == 'itemLevel') {
                        return appliedWrapping.hasOwnProperty(this.itemId) ? appliedWrapping[this.itemId] : null;
                    }
                    return appliedWrapping;
                },
                isExtraOptionsApplied: function () {
                    var appliedPrintedCard = giftWrappingConfig.hasOwnProperty('appliedPrintedCard')
                        ? giftWrappingConfig['appliedPrintedCard']
                        : false;

                    var appliedGiftReceipt = giftWrappingConfig.hasOwnProperty('appliedGiftReceipt')
                        ? giftWrappingConfig['appliedGiftReceipt']
                        : false;

                    if (appliedGiftReceipt == true) {
                        this.getObservable('wrapping-' + this.itemId, 'giftReceipt')(true);
                    }
                    if (appliedPrintedCard == true) {
                        this.getObservable('wrapping-' + this.itemId, 'printedCard')(true);
                    }

                    return this.itemId == 'orderLevel' && (appliedGiftReceipt || appliedPrintedCard);
                },

                /**
                 * Get gift wrapping price display mode.
                 * @returns {Boolean}
                 */
                displayWrappingBothPrices: function () {
                    return !!giftWrappingConfig.displayWrappingBothPrices;
                },

                /**
                 * Get printed card price display mode.
                 * @returns {Boolean}
                 */
                displayCardBothPrices: function () {
                    return !!giftWrappingConfig.displayCardBothPrices;
                },

                /**
                 * Get gift wrapping price display mode.
                 * @returns {Boolean}
                 */
                displayGiftWrappingInclTaxPrice: function () {
                    return !!giftWrappingConfig.displayGiftWrappingInclTaxPrice;
                },

                /**
                 * Get printed card price display mode.
                 * @returns {Boolean}
                 */
                displayCardInclTaxPrice: function () {
                    return !!giftWrappingConfig.displayCardInclTaxPrice;
                }
            };
        };
    }
);
