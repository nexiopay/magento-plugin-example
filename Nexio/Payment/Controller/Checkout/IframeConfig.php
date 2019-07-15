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
        $this->logger->addDebug('iFrameController is called');	   
        $controllerResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $postParams = rawurldecode(file_get_contents('php://input'));//$this->getRequest()->getPostValue();
        $jsonparm = json_decode($postParams,true);
        $this->logger->addDebug('body: '.$postParams);
        return $this->process($jsonparm,$controllerResult);
    }

    public function process($jsonparm,$controllerResult)
    {
        try {
            if(!is_null($jsonparm) && !empty($jsonparm['billingAddress']) && !empty($jsonparm['totals']))
            {
                $order = $this->checkoutSession->getLastRealOrder();
                $orderId=$order->getEntityId();
                $this->logger->addDebug('order id is: '.$orderId);
                $this->logger->addDebug('original order number is: '.$jsonparm['billingAddress']['ordernumber']);
                
                $order->setStatus('nexio_pending');
                $order->save();

                $jsonparm['billingAddress']['ordernumber'] = $orderId;
                $this->commandPool->get(TransferFactory::GET_ONE_TIME_USE_TOKEN)->execute($jsonparm);
                $result = $this->registry->registry(TransactionGetOTUT::NEXIO_ONE_TIME_USE_TOKEN_KEY);
                
            }
            else
            {
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

