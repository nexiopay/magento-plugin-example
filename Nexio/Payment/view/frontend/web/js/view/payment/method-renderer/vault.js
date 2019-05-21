/*browser:true*/
/*global define*/
define([
    'jquery',
    'Magento_Vault/js/view/payment/method-renderer/vault',
    'Magento_Ui/js/model/messageList',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/redirect-on-success',
    'Magento_Customer/js/customer-data'
], function ($, VaultComponent, globalMessageList, fullScreenLoader, redirectOnSuccessAction, custData) {
    'use strict';

    return VaultComponent.extend({
        defaults: {
            template: 'Magento_Vault/payment/form',
            modules: {
                hostedFields: '${ $.parentName }.nexio'
            }
        },

        /**
         * Get last 4 digits of card
         * @returns {String}
         */
        getMaskedCard: function () {
            return this.details.maskedCC;
        },

        /**
         * Get expiration date
         * @returns {String}
         */
        getExpirationDate: function () {
            return this.details.expirationDate;
        },

        /**
         * Get card type
         * @returns {String}
         */
        getCardType: function () {
            return this.details.type;
        },

        isUsCountry: function () {
            return window.checkoutConfig.payment[this.getCode()].isUSCountry;
        },

        /**
         * Get payment method data
         * @returns {Object}
         */
        getData: function () {
            var data = {
                'method': this.code,
                'additional_data': {
                    'public_hash': this.publicHash,
                    'is_us': this.isUsCountry()
                }
            };

            data['additional_data'] = _.extend(data['additional_data'], this.additionalData);

            return data;
        }
    });
});
