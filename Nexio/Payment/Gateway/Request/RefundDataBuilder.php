<?php

namespace Nexio\Payment\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class RefundDataBuilder
 * @package Nexio\Payment\Gateway\Request
 */
class RefundDataBuilder implements \Magento\Payment\Gateway\Request\BuilderInterface
{
    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);
        $paymentId = $this->getPaymentId($paymentDO->getPayment());
        $totalAmnt = $this->getTotalAmount($paymentDO->getOrder());
        return [
            'data' => [
                'amount' => (float)$totalAmnt,
                'paritalAmount' => (float)SubjectReader::readAmount($buildSubject)
            ],
            'id' => $paymentId
        ];
    }

    /**
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @return mixed
     */
    protected function getPaymentId(\Magento\Payment\Model\InfoInterface $payment)
    {
        return $payment->getAdditionalInformation('payment_id');
    }

    /**
     * @param \Magento\Payment\Gateway\Data\OrderAdapterInterface $order
     * @return float
     */
    protected function getTotalAmount(\Magento\Payment\Gateway\Data\OrderAdapterInterface $order)
    {
        return $order->getGrandTotalAmount();
    }
}
