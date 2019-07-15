<?php

namespace Nexio\Payment\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order;
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
        $this->logger->addDebug("handler handle...");
        $paymentDO = SubjectReader::readPayment($handlingSubject);

        // @var \Magento\Sales\Model\Order\Payment $payment 
        $payment = $paymentDO->getPayment();

        if($payment->canCapture())
        {
            $this->logger->addDebug('capture process payment can capture');
            
        }
        else
        {
            $this->logger->addDebug('capture process payment can not capture!!');
        }

    }

    /**
     * @param array $response
     * @return array
     */
    protected function readResponse(array $response)
    {
        $this->logger->addDebug("handler readresponse...");
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

