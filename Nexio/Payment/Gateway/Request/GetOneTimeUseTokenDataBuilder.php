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

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $items = $buildSubject['items'];
        $totals = $buildSubject['totals'];
        $billingAddress = $buildSubject['billingAddress'];

        $cartItems = [];
        foreach ($items as $item) {
            $cartItem = [];
            $cartItem[self::ITEM] = @$item['sku'];
            $cartItem[self::DESCRIPTION] = @$item['description'];
            $cartItem[self::QUANTITY] = @$item['qty'];
            $cartItem[self::PRICE] = @$item['base_row_total_incl_tax'];
            $cartItem[self::TYPE] = self::SALE;
            $cartItems[] = $cartItem;
        }
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
                    self::FIRST_NAME          => @$billingAddress['firstname'],
                    self::LAST_NAME           => @$billingAddress['lastname'],
                    self::BILL_TO_ADDRESS_ONE => @$billingAddress['street'][0],
                    self::BILL_TO_ADDRESS_TWO => @$billingAddress['street'][1],
                    self::BILL_TO_CITY        => @$billingAddress['city'],
                    self::BILL_TO_STATE       => @$billingAddress['regionCode'],
                    self::BILL_TO_POSTAL      => @$billingAddress['postcode'],
                    self::BILL_TO_COUNTRY     => @$billingAddress['countryId'],
                ],
                self::CART               => [
                    self::ITEMS => $cartItems
                ]
            ]
        ];
        if (!empty($cssLink = $this->getCustomCss())) {
            $result[self::UI_OPTIONS] = [
                self::CSS => $this->getCustomCss()
            ];
        }

        return $result;
    }

   
}
