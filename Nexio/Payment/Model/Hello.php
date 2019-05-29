<?php
namespace Nexio\Payment\Model;
use Nexio\Payment\Webapi\HelloInterface;
use Magento\Payment\Gateway\ConfigInterface;

class Hello implements HelloInterface
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

    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    public function name($name) {
        $this->logger->addDebug('webapi get name: '.$name);
        return "Aloha! Hello, " . $name;
    }

    public function test()
    {
        $headerStringValue = $_SERVER['HTTP_NEXIO_SIGNATURE'];

        if($headerStringValue === null)
                $this->logger->addDebug('signature header not found');
        else
                $this->logger->addDebug('signature header: '.$headerStringValue);
        $post = file_get_contents('php://input');
        $this->logger->addDebug('body of callbackdata: '.$post);

    }

    public function loadsecret()
    {
        $this->logger->addDebug('load secret works!!!');
        
        
        $result = $this->get_secret() ;
        
        if($result === "error")
        {
            $this->logger->addDebug('load secret get error!!!');
            return false;
        }
        else
        {
            $this->logger->addDebug('get secret: '.$result);
            return true;
        }
        

    }

	/**
	 * get_secret
	 * get the share secret of merchant
	 * @since 0.0.5
	 * @return string
	 * 
	 */
    
	private function get_secret()
	{
		try {
			$basicauth = $this->getAuthorization();//"Basic ". base64_encode($this->user_name . ":" . $this->password);
            $this->logger->addDebug("basicauth is: ".$basicauth);
            $requesturl = "https://api.nexiopaysandbox.com/webhook/v3/merchantWebhookSecret/100039";//$this->getUrl('webhook/v3/merchantWebhookSecret/'.'100039');
            $this->logger->addDebug("requesturl is: ".$requesturl);
			$ch = curl_init($requesturl);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Authorization: $basicauth",
				"Content-Type: application/json"));
			$result = curl_exec($ch);
			$error = curl_error($ch);
			curl_close($ch);
			
			$this->logger->addDebug('get secret response: '.$result);
			if ($error) {
				$this->logger->addDebug("get secret get error, return error");
				return "error";
			} else {
                if(!empty(json_decode($result)->error) || empty(json_decode($result)->secret))
                {
                    $this->logger->addDebug("no correct message, return error");
				    return "error";
                }

                /*
				if(json_decode($result)->error)
				{
					
					return "error";
                }*/
				
				$secret = json_decode($result)->secret;
				error_log('get secret: '.$secret);
				return $secret;
			}
		} catch (Exception $e) {
			
			$this->logger->addDebug("Get secret failed:".$e->getMessage(),0);
			return "error";
		}
    }
    
   
    /**
     * @return string
     */
    private function getAuthorization()
    {
        return 'Basic ' . base64_encode("samlu@cmsonline.com" . ':' . "Lujunji@791218");
    }
}
