<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Nexio\Payment\Test\Unit\Controller\Checkout;

use Magento\Braintree\Controller\Paypal\PlaceOrder;
use Magento\Braintree\Gateway\Config\PayPal\Config;
use Magento\Braintree\Model\Paypal\Helper\OrderPlace;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Model\Quote;

//Abstract Controller needs
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\ConfigInterface;

//real IframeConfig need
use Nexio\Payment\Gateway\Http\TransferFactory;
use Nexio\Payment\Gateway\Http\Client\TransactionGetOneTimeUseToken as TransactionGetOTUT;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\DB\TransactionFactory;

use Nexio\Payment\Controller\Checkout\IframeConfig;

class IframeConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var OrderPlace|\PHPUnit_Framework_MockObject_MockObject
     */
    private $orderPlaceMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var Session|\PHPUnit_Framework_MockObject_MockObject
     */
    private $checkoutSessionMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultFactoryMock;

    /**
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var IframeConfig
     */
    private $IframeConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;


    private $customerSessionMock;

    private $commandPoolMock;

    private $registryMock;

    private $encryptorMock;

    private $orderFactoryMock;

    private $transactionFactoryMock;

    protected function setUp()
    {
        /** @var Context|\PHPUnit_Framework_MockObject_MockObject $contextMock */
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(\Nexio\Payment\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
       
        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
        ->disableOriginalConstructor()
        ->getMock();


        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerSessionMock = $this->getMockBuilder(CustomerSession::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->commandPoolMock = $this->getMockBuilder(CommandPoolInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->registryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->encryptorMock = $this->getMockBuilder(EncryptorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutSessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->orderFactoryMock = $this->getMockBuilder(OrderFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->transactionFactoryMock = $this->getMockBuilder(TransactionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
            

        $contextMock->expects(self::once())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);    

        $this->IframeConfig = new IframeConfig(
            $contextMock,
            $this->configMock,
            $this->customerSessionMock,
            $this->commandPoolMock,
            $this->loggerMock,
            $this->registryMock,
            $this->encryptorMock,
            $this->checkoutSessionMock,
            $this->orderFactoryMock,
            $this->transactionFactoryMock
        );
    }

    public function testExecute()
    {
        //IframeConfig needs
        $result = 'TOKENFEOMNEXIO';
        
        $resultMock = $this->getResultMock();
        $resultMock->expects(self::once())
            ->method('setData')
            ->with($result)
            ->willReturn($result);

        $this->resultFactoryMock->expects(self::once())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($resultMock);

        $request = array(
            'billingaddress' => array(
                                        'ordernumber' => 123
            ),
            'totals' => array(
                'amount' => 5.99
            )
        );

        $data = json_encode($request);

        stream_wrapper_unregister("php");
        $streammock = new MockPhpStream();
        stream_wrapper_register("php", $streammock);
        file_put_contents('php://input', $data);
           
              

        self::assertEquals($this->IframeConfig->execute(), $result);
        stream_wrapper_restore("php");  
    }

    /**
     * @return ResultInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getResultMock()
    {
        return $this->getMockBuilder(ResultInterface::class)
            ->setMethods(['setData'])
            ->getMockForAbstractClass();
    }

}


class MockPhpStream{
    protected $index = 0;
    protected $length = null;
    protected $data = 'hello world';

    public $context;

    function __construct(){
        if(file_exists($this->buffer_filename())){
        $this->data = file_get_contents($this->buffer_filename());
        }else{
        $this->data = '';
        }
        $this->index = 0;
        $this->length = strlen($this->data);
    }

    protected function buffer_filename(){
    return sys_get_temp_dir().'\php_input.txt';
    }

    function stream_open($path, $mode, $options, &$opened_path){
    return true;
    }

    function stream_close(){
    }

    function stream_stat(){
    return array();
    }

    function stream_flush(){
    return true;
    }

    function stream_read($count){
    if(is_null($this->length) === TRUE){
    $this->length = strlen($this->data);
    }
    $length = min($count, $this->length - $this->index);
    $data = substr($this->data, $this->index);
    $this->index = $this->index + $length;
    return $data;
    }

    function stream_eof(){
    return ($this->index >= $this->length ? TRUE : FALSE);
    }

    function stream_write($data){
    return file_put_contents($this->buffer_filename(), $data);
    }

    function unlink(){
    if(file_exists($this->buffer_filename())){
    unlink($this->buffer_filename());
    }
    $this->data = '';
    $this->index = 0;
    $this->length = 0;
    }
}


