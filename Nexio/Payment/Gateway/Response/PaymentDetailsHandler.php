<?php

namespace Nexio\Payment\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class PaymentDetailsHandler
 * @package Nexio\Payment\Gateway\Response
 */
class PaymentDetailsHandler extends AbstractHandler
{
    /**
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = SubjectReader::readPayment($handlingSubject);

        $response = $this->readResponse($response);

        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentDO->getPayment();

        $payment->setCcTransId(@$response['ref_number']);
        $payment->setLastTransId(@$response['ref_number']);
        $payment->setTransactionId(@$response['ref_number']);
        $payment->setShouldCloseParentTransaction(false);
        $payment->setIsTransactionClosed(false);

        $additionalInfo = $payment->getAdditionalInformation();
        $last4 = @$additionalInfo['last4'];
        $cardType = @$additionalInfo['card_type'];
        $expMonth = @$additionalInfo['expMonth'];
        $expYear = @$additionalInfo['expYear'];

        $payment->setCcLast4($last4);
        $payment->setCcType($cardType);
        $payment->setCcExpMonth($expMonth);
        $payment->setCcExpYear($expYear);

//         set card details to additional info
        try {
            $payment->setAdditionalInformation('cc_number', 'xxxx-' . $last4);
            $payment->setAdditionalInformation('cc_type', $cardType);
            $payment->setAdditionalInformation('ref_number', @$response['ref_number']);
            $payment->setAdditionalInformation('gateway_name', @$response['gateway_name']);
            $payment->setAdditionalInformation('auth_code', @$response['auth_code']);
            $payment->setAdditionalInformation('status', @$response['status']);
            $payment->setAdditionalInformation('payment_id', @$response['payment_id']);
        } catch (LocalizedException $e) {
            $this->logger->addDebug("Exception when handling payment details");
        }
    }

    /**
     * @param array $response
     * @return array
     */
    protected function readResponse(array $response)
    {
        $body = @$response['body'];
        if (!is_array($body)) {
            return [];
        }
        return [
            'ref_number' => @$body['gatewayResponse']['refNumber'],
            'gateway_name' => @$body['gatewayResponse']['gatewayName'],
            'auth_code' => @$body['authCode'],
            'status' => @$body['transactionStatus'],
            'payment_id' => @$body['id']
        ];
    }
}
