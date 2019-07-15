<?php

namespace Nexio\Payment\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class GetOneTimeUseTokenDataBuilder
 * @package Nexio\Payment\Gateway\Request
 */
class GetOneTimeUseTokenDataBuilder extends AbstractDataBuilder
{
    const DATA = 'data';

    const PAYMENT_METHOD = 'paymentMethod';
    const CURRENCY = 'currency';
    const AMOUNT = 'amount';
    const PARTIAL_AMOUNT = 'partialAmount';
    const DESCRIPTION = 'description';
    const ALLOWED_CARD_TYPES = 'allowedCardTypes';
    const CUSTOM_FIELDS = 'customFields';

    const UI_OPTIONS = 'uiOptions';
    const CSS = 'css';

    const CUSTOMER = 'customer';
    const FIRST_NAME = 'firstName';
    const LAST_NAME = 'lastName';
    const BILL_TO_ADDRESS_ONE = 'billToAddressOne';
    const BILL_TO_ADDRESS_TWO = 'billToAddressTwo';
    const BILL_TO_CITY = 'billToCity';
    const BILL_TO_STATE = 'billToState';
    const BILL_TO_POSTAL = 'billToPostal';
    const BILL_TO_COUNTRY = 'billToCountry';

    const CART = 'cart';
    const ITEMS = 'items';
    const ITEM = 'item';
    const QUANTITY = 'quantity';
    const PRICE = 'price';
    const TYPE = 'type';

    const CREDIT_CARD = 'creditCard';
    const SALE = 'sale';

    const CARD = 'card';
    const CARDHOLDER_NAME = 'cardHolderName';

    const CHECK_FRAUD = 'checkFraud';
    const VERBOSE_RESPONSE = 'verboseResponse';
    const SAVE_CARD_TOKEN = 'saveCardToken';

    const DISPLAY_SUBMITBUTTON = 'displaySubmitButton';
    const HIDE_CVC = 'hideCvc';
    const REQUIRE_CVC = 'requireCvc';
    const HIDE_BILLING = 'hideBilling';
    const CUSTOM_TEXT_URL = 'customTextUrl';
    const PROCESSING_OPTIONS = 'processingOptions';
    const AUTH_ONLY = 'isAuthOnly';
    const WEBHOOK_URL = 'webhookUrl';
    const WEBHOOKFAIL_URL = 'webhookFailUrl';
    const ORDER_NUMBER = "orderNumber";

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $totals = $buildSubject['totals'];
        $billingAddress = $buildSubject['billingAddress'];

        
        $result = [
            self::DATA => [
                self::PAYMENT_METHOD     => self::CREDIT_CARD,
                self::CURRENCY           => @$totals['base_currency_code'],
                self::AMOUNT             => @$totals['base_grand_total'],
                self::PARTIAL_AMOUNT     => @$totals['base_grand_total'],
                self::DESCRIPTION        => "DES",
                self::ALLOWED_CARD_TYPES => [
                    "visa",
                    "mastercard",
                    "discover",
                    "amex"
                ],
                self::CUSTOMER           => [
                    self::ORDER_NUMBER        => @$billingAddress['ordernumber'],
                    self::FIRST_NAME          => @$billingAddress['firstname'],
                    self::LAST_NAME           => @$billingAddress['lastname'],
                    self::BILL_TO_ADDRESS_ONE => @$billingAddress['street1'],//[0],
                    self::BILL_TO_ADDRESS_TWO => @$billingAddress['street2'],
                    self::BILL_TO_CITY        => @$billingAddress['city'],
                    self::BILL_TO_STATE       => @$billingAddress['regionCode'],
                    self::BILL_TO_POSTAL      => @$billingAddress['postcode'],
                    self::BILL_TO_COUNTRY     => @$billingAddress['countryId'],
                ]
            ]
        ];

        //processing_options
        
        $result[self::PROCESSING_OPTIONS] = [
            self::WEBHOOK_URL => "https://".$_SERVER['HTTP_HOST']."/rest/V1/webhook/success",
            self::CHECK_FRAUD => $this->getFraudCheck()?true:false,
            self::VERBOSE_RESPONSE => false,
            self::SAVE_CARD_TOKEN => false,
            //todo callback URL and failure callback url
        ];


        //uiOptions
        $css = $this->getCustomCss();
        if(empty($css))
            $css = '';

        $hidecvc = $this->getHideCvc()?true:false;
        $requirecvc = $this->getRequireCvc()?true:false;
        $hidebilling = $this->getHideBilling()?true:false;  
        $customtexturl = $this->getCustomTextFile();
        if(empty($customtexturl))
            $customtexturl = '';

        $result[self::UI_OPTIONS] = [
            self::CSS => $css,
            self::DISPLAY_SUBMITBUTTON => false,
            self::HIDE_CVC => $hidecvc,
            self::REQUIRE_CVC => $requirecvc,
            self::HIDE_BILLING => $hidebilling,
            self::CUSTOM_TEXT_URL => $customtexturl,
        ];


        //card
        $result[self::CARD] = [
            self::CARDHOLDER_NAME => @$billingAddress['firstname'].' '.@$billingAddress['lastname'],
        ];

        //isAuthOnly
        $result[self::AUTH_ONLY] = $this->getAuthOnly()?true:false;

        
        return $result;
    }

}


