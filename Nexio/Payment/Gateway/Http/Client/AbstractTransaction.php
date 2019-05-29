<?php

namespace Nexio\Payment\Gateway\Http\Client;
use Magento\Payment\Gateway\ConfigInterface;
/**
 * Class AbstractTransaction
 * @package Nexio\Payment\Gateway\Http\Client
 */
abstract class AbstractTransaction implements \Magento\Payment\Gateway\Http\ClientInterface
{
    /**
     * @var \Nexio\Payment\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Zend\Http\ClientFactory
     */
    protected $clientFactory;

    /**
     * @var \Zend\Http\HeadersFactory
     */
    protected $headersFactory;

    /**
     * @var \Zend\Http\RequestFactory
     */
    protected $requestFactory;


    /**
     * AbstractTransaction constructor.
     * @param \Nexio\Payment\Logger\Logger $logger
     * @param \Zend\Http\ClientFactory $clientFactory
     * @param \Zend\Http\RequestFactory $requestFactory
     * @param \Zend\Http\HeadersFactory $headersFactory
     */
    public function __construct(
        \Nexio\Payment\Logger\Logger $logger,
        \Zend\Http\ClientFactory $clientFactory,
        \Zend\Http\RequestFactory $requestFactory,
	\Zend\Http\HeadersFactory $headersFactory
    ) {
        $this->logger = $logger;
        $this->clientFactory = $clientFactory;
        $this->headersFactory = $headersFactory;
	$this->requestFactory = $requestFactory;
    }

    /**
     * @param \Magento\Payment\Gateway\Http\TransferInterface $transferObject
     * @return array
     */
    public function placeRequest(\Magento\Payment\Gateway\Http\TransferInterface $transferObject)
    {
        $data = $transferObject->getBody();
        $response = [];
        try {
            $response = $this->process($transferObject->getUri(), $transferObject->getHeaders(), $data);
        } catch (\Exception $e) {
        }
        return $response;
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param array $data
     * @return array
     */
    protected function process($uri, array $headers, array $data)
    {
        $return = [];
        $httpHeaders = $this->getHttpHeaders($headers);
        $httpRequest = $this->getHttpRequest($uri, $httpHeaders);
        $httpClient = $this->getHttpClient($httpRequest, $data);

        try {
            $response = $httpClient->send();
            $return['status_code'] = $response->getStatusCode();
            $return['phrase'] = $response->getReasonPhrase();
            $return['body'] = $response->getBody();
        } catch (\Exception $e) {
            $this->logger->addDebug("Exception when calling api: \n" . $e->getMessage());
            $this->logger->addDebug("Body was sent: \n" . print_r($data, true));
            $return['error'] = $e->getCode();
            $return['error_message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * @param array $headers
     * @return \Zend\Http\Headers
     */
    protected function getHttpHeaders(array $headers)
    {
        /** @var \Zend\Http\Headers $httpHeaders */
        $httpHeaders = $this->headersFactory->create();
        $httpHeaders->addHeaders($headers);
        return $httpHeaders;
    }

    /**
     * @param string $uri
     * @param \Zend\Http\Headers $headers
     * @param string $method
     * @return \Zend\Http\Request
     */
    protected function getHttpRequest($uri, $headers, $method = \Zend\Http\Request::METHOD_POST)
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->requestFactory->create();
        $request->setUri($uri);
        $request->setMethod($method);
        $request->setHeaders($headers);
        return $request;
    }

    /**
     * @param \Zend\Http\Request $request
     * @param array|string $body
     * @return \Zend\Http\Client
     */
    protected function getHttpClient($request, $body)
    {
        /** @var \Zend\Http\Client $client */
        $client = $this->clientFactory->create();
        $client->setRequest($request);
        $client->setOptions(['timeout' => 30]);
        if (is_array($body)) {
            $body = json_encode($body);
        }
        $client->setRawBody($body);
        return $client;
    }
}
