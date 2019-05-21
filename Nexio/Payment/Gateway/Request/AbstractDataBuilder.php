<?php

namespace Nexio\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\ConfigInterface;

/**
 * Class AbstractDataBuilder
 * @package Nexio\Payment\Gateway\Request
 */
abstract class AbstractDataBuilder implements BuilderInterface
{
    /**
     * @var \Magento\Vault\Api\PaymentTokenManagementInterface
     */
    protected $paymentTokenManagement;

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * AbstractDataBuilder constructor.
     *
     * @param ConfigInterface $config
     * @param \Magento\Vault\Api\PaymentTokenManagementInterface $paymentTokenManagement
     */
    public function __construct(
        ConfigInterface $config,
        \Magento\Vault\Api\PaymentTokenManagementInterface $paymentTokenManagement
    ) {
        $this->config = $config;
        $this->paymentTokenManagement = $paymentTokenManagement;
    }

    /**
     * @param $publicHash
     * @param $customerId
     * @return null|string
     */
    private function _getToken($publicHash, $customerId)
    {
        return !$publicHash ? null :
            $this->paymentTokenManagement->getByPublicHash($publicHash, $customerId)->getGatewayToken();
    }

    /**
     * @param \Magento\Payment\Gateway\Data\PaymentDataObjectInterface $paymentDO
     * @return null|string
     */
    protected function getToken(\Magento\Payment\Gateway\Data\PaymentDataObjectInterface $paymentDO)
    {
        $paymentInfo = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();
        return $paymentInfo->getAdditionalInformation('token') ?:
            $this->_getToken($paymentInfo->getAdditionalInformation('public_hash'), $order->getCustomerId());
    }

    /**
     * @param \Magento\Payment\Gateway\Data\PaymentDataObjectInterface $paymentDO
     * @return array
     */
    protected function getExpDate(\Magento\Payment\Gateway\Data\PaymentDataObjectInterface $paymentDO)
    {
        $paymentInfo = $paymentDO->getPayment();
        $expMonth = $paymentInfo->getAdditionalInformation('expMonth');
        $expYear = $paymentInfo->getAdditionalInformation('expYear');
        if (!isset($expMonth, $expYear)) {
            return [];
        }
        return [
            'card' => [
                'expirationMonth' => (string)(int)$expMonth,
                'expirationYear' => substr($expYear, -2),
            ]
        ];
    }

    protected function getCustomCss(){
        return trim($this->config->getValue('custom_css'));
    }

    protected function getFraudCheck(){
        return $this->config-getValue('fraud_check');
    }

    protected function getHideCvc(){
        return $this->config-getValue('hide_cvc');
    }

    protected function getRequireCvc(){
        return $this->config-getValue('require_cvc');
    }

    protected function getHideBilling(){
        return $this->config-getValue('hide_billing');
    }

    protected function getCustomTextFile(){
        return trim($this->config->getValue('customer_textfile'));
    }

    protected function getAuthOnly(){
        return $this->config-getValue('auth_only');
    }

}
