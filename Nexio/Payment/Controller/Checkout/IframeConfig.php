<?php

namespace Nexio\Payment\Controller\Checkout;

use Nexio\Payment\Gateway\Http\TransferFactory;
use Nexio\Payment\Gateway\Http\Client\TransactionGetOneTimeUseToken as TransactionGetOTUT;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class IframeConfig
 * @package Nexio\Payment\Controller\Checkout
 */
class IframeConfig extends AbstractCheckoutController
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $controllerResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $postParams = $this->getRequest()->getPostValue();
        try {
            if (@$postParams['billingAddress'] &&
                json_decode(@$postParams['billingAddress'], true) &&
                is_array(json_decode(@$postParams['billingAddress'], true))) {
                $postParams['billingAddress'] = json_decode(@$postParams['billingAddress'], true);
                $this->commandPool->get(TransferFactory::GET_ONE_TIME_USE_TOKEN)->execute($postParams);
                $result = $this->registry->registry(TransactionGetOTUT::NEXIO_ONE_TIME_USE_TOKEN_KEY);
            } else {
                $result = false;
            }
        } catch (\Exception $e) {
            $result = false;
            $this->logger->addDebug("Exception when getting token: " . $e->getMessage());
            $this->messageManager->addErrorMessage(__("Something went wrong. Please try again later"));
        }
        return $controllerResult->setData($result);
    }
}
