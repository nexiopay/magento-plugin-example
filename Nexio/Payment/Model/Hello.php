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
        
        
        $result = $this->callGetSecret() ;
        

    }

    private function callGetSecret()
    {
        try {
            $requesturl = "https://".$_SERVER['HTTP_HOST']."/index.php/nexio/checkout/getsecretConfig/";//"https://mag.cmsshanghaidev.com/index.php/nexio/checkout/getsecretConfig/";//$this->getUrl('webhook/v3/merchantWebhookSecret/'.'100039');
            $this->logger->addDebug("HTTP requesturl is: ".$requesturl);
            $ch = curl_init($requesturl);
            
            $request = array(
                'command' => 'getsecret'
            );
            $data = json_encode($request);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json"));//,
               // "Content-Length: " . strlen($data)
                //));
			$result = curl_exec($ch);
			$error = curl_error($ch);
			curl_close($ch);
			
			$this->logger->addDebug('Hello get secret response: '.$result);
			if ($error) {
				$this->logger->addDebug("Hello get secret get error, return error");
				
			} else {
                //do nothing
                $this->logger->addDebug("Hello get secret success");
			}
		} catch (Exception $e) {
			
			$this->logger->addDebug("Hello Get secret failed:".$e->getMessage(),0);
			return "error";
		}
    }

	
}

