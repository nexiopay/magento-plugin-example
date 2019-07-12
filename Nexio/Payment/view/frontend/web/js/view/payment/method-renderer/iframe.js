define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/error-processor',
        'mage/translate',
        'Magento_Ui/js/modal/alert',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/model/full-screen-loader',
        'mage/url',
        'Magento_Vault/js/view/payment/vault-enabler',
        'Magento_Checkout/js/action/redirect-on-success',
        'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function ($,
              Component,
              quote,
              customer,
              errorProcessor,
              $t,
              alert,
              modal,
              fullScreenLoader,
              url,
              VaultEnabler,
              redirectOnSuccessAction,
              additionalValidators) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Nexio_Payment/payment/iframe',
                title: $t('Credit Card Payment'),
                iframeUrl: '',
                savedCard: '',
                isValid: false,
                iframeLoaded: false,
                redirectAfterPlaceOrder: true
            },
            initialize: function () {
                this._super();
                this.vaultEnabler = VaultEnabler();
                this.vaultEnabler.setPaymentCode(this.getVaultCode());
                return this;
            },
            initObservable: function () {
                this._super().observe(['isValid']);
                return this;
            },
            getCode: function () {
                return 'nexio';
            },
            getVaultCode: function () {
                return 'nexio_vault';
            },
            isValid: function () {
                
                return true;
            },
            showiFrame:function(){
                var self = this;
                //need set this to true otherwise iFrame is not working
                self.isPlaceOrderActionAllowed(true);
                if (!self.iframeLoaded) {
                    fullScreenLoader.startLoader();
                }
                if (!self.hasModal()) {
                    var postdata = {
                        'billingAddress':{
                            'ordernumber':quote.getQuoteId(),
                            'firstname':quote.billingAddress().firstname,
                            'lastname':quote.billingAddress().lastname,
                            'street1':quote.billingAddress().street[0],
                            'street2':quote.billingAddress().street[1],
                            'city':quote.billingAddress().city,
                            'regionCode':quote.billingAddress().regionCode,
                            'postcode':quote.billingAddress().postcode,
                            'countryId':quote.billingAddress().countryId
                        },
                        'totals':{
                            'base_currency_code':quote.totals().base_currency_code,
                            'base_grand_total':quote.totals().base_grand_total
                        }
                    };
                    
                    $.ajax({
                        url: self.getIframeUrl(),
                        showLoader: true,
                        data: JSON.stringify(postdata),
                        type: 'POST'
                    }).done(function (response) {
                        if (response) {
                            var iframeBaseUrl = self.iframeBaseUrl(),
                                iframe = window.document.getElementById('nexio-iframe');
                            self.iframeUrl = iframeBaseUrl + response;
                            iframe.src = self.iframeUrl;
                            window.addEventListener('message', self.messageListener.bind(self));
                            self.openModal();
                        } else {
                            self.alertError(
                                $t('Failed to get token.'),
                                url.build('checkout/cart/')
                            );
                        }
                    }).fail(self.failCallback.bind(self));
                } else {
                    self.openModal();
                }
            },
            redisplayiframe: function()
            {
                var self = this;
                if (self.iframeUrl) {
                    console.log("iframeUrl is not null, redisplay it");
                    var iframe = window.document.getElementById('nexio-iframe');
                    iframe.src = self.iframeUrl;
                    window.addEventListener('message', self.messageListener.bind(self));
                    self.openModal();
                } else {
                   console.log("first time submit, call beforePlaceOrder first.");
                   self.beforePlaceOrder();
                }
            },
            beforePlaceOrder: function () {
                var self = this;
                self.placeOrder();
            },
            placeOrder: function (data, event) {
                var self = this;

                if (event) {
                    event.preventDefault();
                }

                if (this.validate() && additionalValidators.validate()) {
                    console.log('this.validate passed');
                    this.isPlaceOrderActionAllowed(false);
                    
                    this.getPlaceOrderDeferredObject()
                        .fail(
                            function () {
                                console.log('getPlaceOrderDeferredObject failed');
                                fullScreenLoader.stopLoader();
                                self.isPlaceOrderActionAllowed(true);
                            }
                        ).done(
                            function () {
                                console.log('getPlaceOrderDeferredObject success');
                                var result = self.afterPlaceOrder();
                            }
                        );
                    
                    return true;
                }
                
                return true;
            },
            afterPlaceOrder: function() {
                var self = this;
                self.showiFrame();
                
                return false;
            },
            isVaultEnabled: function () {
                return this.vaultEnabler.isVaultEnabled();
            },
            openModal: function () {
                this.getModal().modal('openModal').show();
                $('body').css('position', 'fixed');
            },
            closeModal: function () {
                this.getModal().modal('closeModal');
            },
            hasModal: function () {
                return !!this.modal && this.iframeLoaded;
            },
            getModal: function () {
                if (!this.hasModal()) {
                    this.modal = $('#nexio-iframe-modal').modal(this.getModalOptions());
                    if (!/iPhone|iPod|iPad/.test(navigator.userAgent)) {
                        $('style#style-ios').remove();
                    }
                }
                return this.modal;
            },
            getModalOptions: function () {
                return {
                    title: this.getTitle(),
                    buttons: this.getModalButtons(),
                    responsive: true,
                    closed: function () {
                        $('body').css('position', 'static');
                    }
                };
            },
            getModalButtons: function () {
                return false;
            },
            getTitle: function () {
                return this.title;
            },
            failCallback: function (response) {
                var self = this,
                    resultCallback = function () {
                        fullScreenLoader.stopLoader();
                        errorProcessor.process(response, self.messageContainer);
                    };
                $.post(self.getCancelOrderUrl())
                    .done(resultCallback)
                    .fail(resultCallback);
            },
            messageListener: function (event) {
                var self = this,
                    data = event.data;
		    console.log('get event: ' + JSON.stringify(data));
                if (self.iframeUrl.indexOf(event.origin) === 0) {
                    if (data.event === 'error') {
			            console.log('get data error from Nexio: ' + JSON.stringify(data));
                        self.alertError(
                            $t('Transaction was declined. Please try again later.'),
                            url.build('checkout/cart/')
                        );
                    } else if (data.event === 'formValidations') {
                        var validData = data.data,
                            isValid = true;
                        for (var v in validData) {
                            if (validData.hasOwnProperty(v) && validData[v] === true) {
                                continue;
                            }
                            isValid = false;
                            break;
                        }
                        self.isValid(isValid);
                    } else if (data.event === 'processed') {
			            console.log('get data success from Nexio: ' + JSON.stringify(data));
                            self.closeModal();
                            self.iframeLoaded = false;
                            if (self.redirectAfterPlaceOrder) {
                                redirectOnSuccessAction.execute();
                            }
                    } else if (data.event === 'loaded') {
                        self.iframeLoaded = true;
                        fullScreenLoader.stopLoader();
                    }
                }
            },
            getData: function () {
                var data = {
                    'method': this.item.method,
                    'po_number': null,
                    'additional_data': this.savedCard ?
                        {
                            'token': this.savedCard.token.token,
                            'card_type': this.savedCard.token.cardType,
                            'first6': this.savedCard.token.firstSix,
                            'last4': this.savedCard.token.lastFour,
                            'expMonth': this.savedCard.card.expirationMonth,
                            'expYear': this.savedCard.card.expirationYear
                        }
                        : this._super()
                };
                this.vaultEnabler.visitAdditionalData(data);
                return data;
            },
            submitForm: function () {
                fullScreenLoader.startLoader();
                window.document.getElementById('nexio-iframe').contentWindow.postMessage('posted', this.iframeUrl);
            },
            alertError: function (content, config) {
                var callback = null;
                if (typeof config === 'string') {
                    callback = function () {
                        window.location.replace(config);
                    }
                } else if ($.isFunction(config)) {
                    callback = config;
                }
                var alertConfig = {
                    content: content
                };
                if (callback !== null) {
                    alertConfig.buttons = [{
                        text: $t('OK'),
                        class: 'action-primary action-accept',
                        click: callback
                    }];
                }
                fullScreenLoader.stopLoader();
                alert(alertConfig);
            },
            iframeBaseUrl: function () {
                return window.checkoutConfig.payment[this.getCode()].iframeBaseUrl;
            },
            getIframeUrl: function () {
                return window.checkoutConfig.payment[this.getCode()].getIframeUrl;
            },
            getSecretUrl: function(){
                return window.checkoutConfig.payment[this.getCode()].getSecretUrl;
            }
        });
    }
);



