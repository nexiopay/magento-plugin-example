<?php

namespace Nexio\Payment\Gateway\Http\Client;


/**
 * Class TransactionAuthorize
 * @package Nexio\Payment\Gateway\Http\Client
 */
class TransactionAuthorize extends AbstractTransaction
{
    /**
     * @param string $uri
     * @param array $headers
     * @param array $data
     * @return array
     */
    public function process($uri, array $headers, array $data)
    {   
        $this->logger->addDebug("Begin Auth trans process");
        
        $body = array(
            "result" => "this is a test"
        );

        $response['status_code'] = 200;
        $response['phrase'] = 'OK';
        $response['body'] = json_encode($body);

        

        return $result;
    }
}

