<?php

namespace Nexio\Payment\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class AuthorizeDataBuilder
 * @package Nexio\Payment\Gateway\Request
 */
class AuthorizeDataBuilder extends AbstractDataBuilder
{
    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDataObj = SubjectReader::readPayment($buildSubject);

        $paymentInfo = $paymentDataObj->getPayment();
        //$token = $paymentInfo->getAdditionalInformation('token');

        if (!$token) {
            return [];
        }

        $order = $paymentDataObj->getOrder();
        $currency = $order->getCurrencyCode();

        return [
            'isAuthOnly' => true,
            'tokenex' => [
                'token' => "test token value"
            ],
            'data' => [
                'amount' => 0.99,
                'currency' => $currency
            ]
        ];
    }
}


