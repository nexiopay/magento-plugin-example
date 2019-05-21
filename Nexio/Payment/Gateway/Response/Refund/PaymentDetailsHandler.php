<?php

namespace Nexio\Payment\Gateway\Response\Refund;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Nexio\Payment\Gateway\Response\AbstractHandler;

/**
 * Class PaymentDetailsHandler
 * @package Nexio\Payment\Gateway\Response\Refund
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

        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentDO->getPayment();
    }
}
