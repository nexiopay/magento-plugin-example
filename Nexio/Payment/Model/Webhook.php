<?php
namespace Nexio\Payment\Model;
use Nexio\Payment\Webapi\WebhookInterface;
use Magento\Payment\Gateway\ConfigInterface;

class Webhook implements WebhookInterface
{
    protected $request;
    
    /**
     * @var \Nexio\Payment\Logger\Logger
     */
    protected $logger;

    /**
     * @var ConfigInterface
     */
    protected $config;
    
    public function __construct(
        \Nexio\Payment\Logger\Logger $logger//,
        //ConfigInterface $config
    ) {
        $this->logger = $logger;
        //$this->config = $config;
    }

    public function success()
    {
        $headerStringValue = $_SERVER['HTTP_NEXIO_SIGNATURE'];

        if($headerStringValue === null)
            $this->logger->addDebug('signature header not found');
        else
            $this->logger->addDebug('signature header: '.$headerStringValue);
        $post = file_get_contents('php://input');
        $this->logger->addDebug('body of callbackdata: '.$post);

        $param = json_decode($post,true);

        /*
        try
        {
            $orderId = $param['data']['data']['customer']['orderNumber'];
            $this->logger->addDebug('OrderId: '.$param['data']['data']['customer']['orderNumber']);
            $order = Mage::getModel('sales/order')->load($orderId);

            $orderNum = $order->getIncrementId();
            $this->logger->addDebug('OrderNum: '.$orderNum);
        }
        catch(Exception $e)
        {
            $this->logger->addDebug('order info exception: '.$e->getMessage());
        }
        */
        
        if(empty($param->data->merchantId))
            $merchantId = "";
        else
            $merchantId = $param->data->merchantId;
        

        $this->callGetSecret($headerStringValue,$post,$merchantId);



    }

    public function loadsecret()
    {
        $this->logger->addDebug('load secret works!!!');
    }

    private function callGetSecret($headerStringValue,$post,$merchantId)
    {
        try {
            $requesturl = "https://".$_SERVER['HTTP_HOST']."/index.php/nexio/checkout/getsecretConfig/?command=getsecret&merchantId=".$merchantId;//"https://mag.cmsshanghaidev.com/index.php/nexio/checkout/getsecretConfig/";//$this->getUrl('webhook/v3/merchantWebhookSecret/'.'100039');
            $this->logger->addDebug("HTTP requesturl is: ".$requesturl);
            $ch = curl_init($requesturl);
            
            
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            //    "Content-Type: application/json"));
			$result = curl_exec($ch);
			$error = curl_error($ch);
			curl_close($ch);
			
			$this->logger->addDebug('Hello get secret response: '.$result);
			if ($error) {
				$this->logger->addDebug("Hello get secret get error, return error");
				
			} else {
                $this->logger->addDebug("Hello get secret success");

                //flag of go aheard or not.
                $verifypassed = true;

                //flag of signature verification bypassed
                $bypassverifiy = true;
                $param = json_decode($result);
                $verifyflag = false;
                if(!empty($param->verifyflag))
                    $verifyflag = !!$param->verifyflag;


                if($verifyflag)
                {
                    $verifypassed = false;
                    $bypassverifiy = false;
                    $this->logger->addDebug('need verify signature');
                    if(!$this->check_signature($headerStringValue,$post,$param->secret))
                    {
                        $this->logger->addDebug('callback signature verification failed!!');
                        
                        
                    }
                    else
                    {
                        $verifypassed = true;
                        $this->logger->addDebug('callback signature verification passed!!');
                    }
                }
                

                if($verifypassed)
                {
                    //todo update order
                    //convert data into request parameters
                    $UpdateOrderParm = json_decode($post);
                    
                    $this->logger->addDebug("eventtype: ");
                    $this->logger->addDebug($UpdateOrderParm->eventType);
                    $this->logger->addDebug("&merchantId=".$UpdateOrderParm->data->merchantId);
                    $this->logger->addDebug("&authCode=".$UpdateOrderParm->data->authCode);
                    $this->logger->addDebug("&amount=".$UpdateOrderParm->data->amount);
                    $this->logger->addDebug("&orderId=".$UpdateOrderParm->data->data->customer->orderNumber);
                    $getparam = "command=updateorder";
                    $getparam = $getparam."&eventType=".$UpdateOrderParm->eventType;
                    $getparam = $getparam."&merchantId=".$UpdateOrderParm->data->merchantId;
                    $getparam = $getparam."&authCode=".$UpdateOrderParm->data->authCode;
                    $getparam = $getparam."&amount=".$UpdateOrderParm->data->amount;
                    $getparam = $getparam."&orderId=".$UpdateOrderParm->data->data->customer->orderNumber;
                    $getparam = $getparam."&verifybypass=".$bypassverifiy;
                    $this->logger->addDebug("UpdateOrderParm string is: ".$getparam);
                    $this->UpdateOrder($getparam);
                }
                else
                {
                    //todo update order with error
                    $UpdateOrderParm = json_decode($post);
                    
                    $this->logger->addDebug("&orderId=".$UpdateOrderParm->data->data->customer->orderNumber);
                    $msg = "Signature|verification|failed!";
                    $getparam = "command=updateorderwitherr";
                    $getparam = $getparam."&orderId=".$UpdateOrderParm->data->data->customer->orderNumber;
                    $getparam = $getparam."&msg=".$msg;
                    $this->logger->addDebug("UpdateOrderParm string is: ".$getparam);
                    $this->UpdateOrderWithErr($getparam);
                    
                    
                }
                
			}
		} catch (Exception $e) {
			
			$this->logger->addDebug("Hello Get secret failed:".$e->getMessage(),0);
			return "error";
		}
    }

    private function UpdateOrder($GetString)
    {
        try {
            $requesturl = "https://".$_SERVER['HTTP_HOST']."/index.php/nexio/checkout/getsecretConfig/?".$GetString;//"https://mag.cmsshanghaidev.com/index.php/nexio/checkout/getsecretConfig/";//$this->getUrl('webhook/v3/merchantWebhookSecret/'.'100039');
            $this->logger->addDebug("HTTP requesturl is: ".$requesturl);
            $ch = curl_init($requesturl);
            
            
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json"));
			$result = curl_exec($ch);
			$error = curl_error($ch);
			curl_close($ch);
			
			$this->logger->addDebug('Hello get UpdateOrder response: '.$result);
			if ($error) {
				$this->logger->addDebug("Hello get UpdateOrder get error, return error");
				
			} else {
                //do nothing
                $this->logger->addDebug("Hello get UpdateOrder success");

                
			}
		} catch (Exception $e) {
			
			$this->logger->addDebug("Hello get UpdateOrder failed:".$e->getMessage(),0);
			return "error";
		}
    }


    private function UpdateOrderWithErr($GetString)
    {
        try {
            $requesturl = "https://".$_SERVER['HTTP_HOST']."/index.php/nexio/checkout/getsecretConfig/?".$GetString;//"https://mag.cmsshanghaidev.com/index.php/nexio/checkout/getsecretConfig/";//$this->getUrl('webhook/v3/merchantWebhookSecret/'.'100039');
            $this->logger->addDebug("HTTP requesturl is: ".$requesturl);
            $ch = curl_init($requesturl);
            
            
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json"));
			$result = curl_exec($ch);
			$error = curl_error($ch);
			curl_close($ch);
			
			$this->logger->addDebug('Hello get UpdateOrderWithErr response: '.$result);
			if ($error) {
				$this->logger->addDebug("Hello get UpdateOrderWithErr get error, return error");
				
			} else {
                //do nothing
                $this->logger->addDebug("Hello get UpdateOrderWithErr success");

                
			}
		} catch (Exception $e) {
			
			$this->logger->addDebug("Hello get UpdateOrderWithErr failed:".$e->getMessage(),0);
			return "error";
		}
    }

    /**
	 * check_signature
	 * check the signature
	 * @since 0.0.5
	 * @param string $nexiosignature 	   the value of nexio-signature header
	 * @param string $rawpayload  		   raw playload of the callback post data
	 * @return bool
	 */
	private function check_signature($nexiosignature,$rawpayload,$shareSecret)
	{
        $this->logger->addDebug('enter check_signature');
		$firstpos = strrpos($nexiosignature,'t=');
		$commonpos = strrpos($nexiosignature, ',');
		$secondpos = strrpos($nexiosignature,'v1=');
		$len = strlen($nexiosignature);

		$timestamp = substr($nexiosignature, $firstpos + 2, $commonpos - 2);
		$signature = substr($nexiosignature, $secondpos + 3, $len - $secondpos - 3);

		$newpayload = $timestamp.'.'.$rawpayload;

		$this->logger->addDebug('shareSecret: '.$shareSecret);
		
		if($shareSecret === 'error' || empty($shareSecret))
		{
			//try to get shareSecret again
			$this->logger->addDebug('shareSecret is not set, or equal to error, verification failed!!');
			return false;
		}

        $this->logger->addDebug('newpayload is: '.$newpayload);
        $this->logger->addDebug('hash_hmac begin');
        $verifysig = hash_hmac('sha256',$newpayload,$shareSecret);
        $this->logger->addDebug('hash_hmac end');
		$this->logger->addDebug('newpayload sig: '.$verifysig);

        if($verifysig === $signature)
        {
            $this->logger->addDebug('check signature passed');
            return true;
        }
        else
		{
            $this->logger->addDebug('check signature failed');
            return false;
        }
	}
	
}

