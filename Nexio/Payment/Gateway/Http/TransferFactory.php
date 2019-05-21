<?php

namespace Nexio\Payment\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Gateway\ConfigInterface;

/**
 * Class TransferFactory
 * @package Nexio\Payment\Gateway\Http
 */
class TransferFactory implements TransferFactoryInterface
{
    const ACTION_AUTHORIZE = 'authorize';
    const ACTION_AUTHORIZE_SCOPE = '/pay/v3/process';
    const ACTION_REFUND = 'refund';
    const ACTION_REFUND_SCOPE = '/pay/v3/refund';

    const GET_ONE_TIME_USE_TOKEN = 'get_one_time_use_token';
    const GET_ONE_TIME_USE_TOKEN_SCOPE = '/pay/v3/token';
    const GET_BASE_IFRAME_URL = 'get_base_iframe_url';
    const GET_BASE_IFRAME_URL_SCOPE = '/pay/v3/';
    const GET_JWT = 'get_jwt';
    const GET_JWT_SCOPE = '/user/v3/login';

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var null|string
     */
    private $action;

    /**
     * TransferFactory constructor.
     * @param ConfigInterface $config
     * @param TransferBuilder $transferBuilder
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param null $action
     */
    public function __construct(
        ConfigInterface $config,
        TransferBuilder $transferBuilder,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        $action = null
    ) {
        $this->transferBuilder = $transferBuilder;
        $this->encryptor = $encryptor;
        $this->config = $config;
        $this->action = $action;
    }

    /**
     * Builds gateway transfer object
     *
     * @param array $request
     * @return TransferInterface
     */
    public function create(array $request)
    {
        return $this->transferBuilder
            ->setBody($request)
            ->setHeaders($this->getHeaders())
            ->setUri($this->getUrl())
            ->build();
    }

    /**
     * @return array
     */
    private function getHeaders()
    {
        $headers = [];
        $headers['Authorization'] = $this->getAuthorization();
        return $headers;
    }

    /**
     * @return string
     */
    private function getAuthorization()
    {
        return 'Basic ' . base64_encode($this->getUsername() . ':' . $this->getPassword());
    }

    /**
     * @return string
     */
    private function getUsername()
    {
        $isTest = $this->getIsTest();
        return $isTest ? $this->config->getValue('test_username') : $this->config->getValue('username');
    }

    /**
     * @return string
     */
    private function getPassword()
    {
        $isTest = $this->getIsTest();
        $pw = $isTest ? $this->config->getValue('test_password') : $this->config->getValue('password');
        return $this->encryptor->decrypt($pw);
    }

    /**
     * @return bool
     */
    private function getIsTest()
    {
        return !!$this->config->getValue('is_test');
    }

    /**
     * @return null|string
     */
    private function getAction()
    {
        return $this->action;
    }

    /**
     * Get request URL
     *
     * @param string $additionalPath
     * @return string
     */
    public function getUrl($additionalPath = '')
    {
        $isTest = $this->getIsTest();

        $uri = $isTest ? $this->config->getValue('test_endpoint') : $this->config->getValue('endpoint');
        $uri = trim($uri);
        $uri = rtrim($uri, '/');
        $url = $uri . $this->getScope();
        return $url;
    }

    /**
     * @return string
     */
    protected function getScope()
    {
        $arr = [
            self::ACTION_AUTHORIZE => self::ACTION_AUTHORIZE_SCOPE,
            self::ACTION_REFUND => self::ACTION_REFUND_SCOPE,
            self::GET_ONE_TIME_USE_TOKEN => self::GET_ONE_TIME_USE_TOKEN_SCOPE,
            self::GET_JWT => self::GET_JWT_SCOPE,
            self::GET_BASE_IFRAME_URL => self::GET_BASE_IFRAME_URL_SCOPE
        ];
        return @$arr[$this->getAction()] ?: '';
    }

    //added by Sam Lu 2019-05-21
    /**
     * @return bool
     */
    private function getAuthOnly()
    {
        return !!$this->config->getValue('auth_only');
    }

    /**
     * @return bool
     */
    private function getHideBilling()
    {
        return !!$this->config->getValue('hide_billing');
    }

    /**
     * @return bool
     */
    private function getHideCVC()
    {
        return !!$this->config->getValue('hide_cvc');
    }

    /**
     * @return bool
     */
    private function getRequireCVC()
    {
        return !!$this->config->getValue('require_cvc');
    }

    /**
     * @return bool
     */
    private function getFraudCheck()
    {
        return !!$this->config->getValue('fraud_check');
    }

    /**
     * @return null|string
     */
    private function getCustomerTextfile()
    {
        $this->config->getValue('customer_textfile');
    }

    

}
