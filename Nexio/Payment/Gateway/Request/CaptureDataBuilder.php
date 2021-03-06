<?php

namespace Nexio\Payment\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class CaptureDataBuilder
 * @package Nexio\Payment\Gateway\Request
 */
class CaptureDataBuilder extends AbstractDataBuilder
{
    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);

        $token = $this->getToken($paymentDO);
        if (!$token) {
            return [];
        }

        $order = $paymentDO->getOrder();
        $currency = $order->getCurrencyCode();

        $return = [
            'isAuthOnly' => false,
            'data' => [
                'currency' => $currency
            ]
        ];

        $expDate = $this->getExpDate($paymentDO);
        $return = array_merge_recursive($return, $expDate);

        return $return;
    }
}

