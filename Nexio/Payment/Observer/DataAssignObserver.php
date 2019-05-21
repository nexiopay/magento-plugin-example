<?php

namespace Nexio\Payment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Class DataAssignObserver
 * @package Nexio\Payment\Observer
 */
class DataAssignObserver extends \Magento\Payment\Observer\AbstractDataAssignObserver
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData) || (!@$additionalData['token'] && !@$additionalData['public_hash'])) {
            return;
        }
        $paymentInfo = $this->readPaymentModelArgument($observer);
        $additionalInfo = $paymentInfo->getAdditionalInformation();
        if (is_array($additionalInfo)) {
            $additionalData = array_merge($additionalInfo, $additionalData);
        }
        $paymentInfo->setAdditionalInformation($additionalData);
    }
}
