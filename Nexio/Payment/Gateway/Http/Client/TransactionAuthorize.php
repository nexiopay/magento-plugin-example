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
        $result = parent::process($uri, $headers, $data);
        if (@$result['phrase'] === 'OK') {
            $result['body'] = json_decode(@$result['body'], JSON_OBJECT_AS_ARRAY);
            if (isset($result['body']['kountResponse']['rules'])) {
                $result['body']['kountResponse']['rules'] = json_decode(
                    $result['body']['kountResponse']['rules'],
                    JSON_OBJECT_AS_ARRAY
                );
            }
        } else {
            $this->logger->addDebug(
                "Error when processing authorize action: \n" .
                print_r($result, true)
            );
            $this->logger->addDebug("Body was sent: \n" . print_r($data, true));
        }
        return $result;
    }
}
