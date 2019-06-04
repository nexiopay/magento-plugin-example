<?php

namespace Nexio\Payment\Gateway\Http\Client;

use Magento\Payment\Gateway\ConfigInterface;

/**
 * Class TransactionGetOneTimeUseToken
 * @package Nexio\Payment\Gateway\Http\Client
 */
class TransactionGetOneTimeUseToken extends AbstractTransaction
{
    const NEXIO_ONE_TIME_USE_TOKEN_KEY = 'nexio_one_time_use_token_key';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    

    /**
     * TransactionGetOneTimeUseToken constructor.
     * @param \Nexio\Payment\Logger\Logger $logger
     * @param \Magento\Framework\Registry $registry
     * @param \Zend\Http\ClientFactory $clientFactory
     * @param \Zend\Http\RequestFactory $requestFactory
     * @param \Zend\Http\HeadersFactory $headersFactory
     */
    public function __construct(
        \Nexio\Payment\Logger\Logger $logger,
        \Magento\Framework\Registry $registry,
        \Zend\Http\ClientFactory $clientFactory,
        \Zend\Http\RequestFactory $requestFactory,
        \Zend\Http\HeadersFactory $headersFactory
        
    )
    {
        parent::__construct($logger, $clientFactory, $requestFactory, $headersFactory);
        $this->registry = $registry;
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param array $data
     * @return array|void
     */
    public function process($uri, array $headers, array $data)
    {
        $result = parent::process($uri, $headers, $data);
        $token = '';
        if (@$result['phrase'] === 'OK') {
            $body = json_decode(@$result['body'], JSON_OBJECT_AS_ARRAY);
	    $token = @$body['token'];
	    $this->logger->addDebug("Get Token has no problem");
        } else {
            $this->logger->addDebug(
                "Error when processing get one time use token: \n" .
                print_r($result, true)
            );
            $this->logger->addDebug("Body was sent: \n" . print_r($data, true));
        }
        $this->registry->register(self::NEXIO_ONE_TIME_USE_TOKEN_KEY, $token);
    }


}


