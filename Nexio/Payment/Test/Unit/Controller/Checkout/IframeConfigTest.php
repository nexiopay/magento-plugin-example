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
use Magento\Sales\Model\Order;

use Nexio\Payment\Controller\Checkout\IframeConfig;
use Nexio\Payment\Logger\Logger;

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
            ->setMethods(['get'])
            ->getMock();

        $this->registryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->setMethods(['registry'])
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

    public function testProcess_success()
    {
        //IframeConfig needs
        $result = 'TOKENFEOMNEXIO';
        
        //need mock order
        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $orderMock->expects(self::once())
            ->method('getEntityId')
            ->willReturn(12);    

        //need 
        $this->checkoutSessionMock->expects(self::once())
            ->method('getLastRealOrder')
            ->willReturn($orderMock);    

        $resultMock = $this->getResultMock();
        $resultMock->expects(self::once())
            ->method('setData')
            ->with($result)
            ->willReturn($result);

        $this->resultFactoryMock->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($resultMock);

        $commandMock = $this->getMockBuilder(GatewayCommand::class)
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMock();

        $commandMock->expects(static::once())
            ->method('execute')
            ->willReturn([]);

        $this->commandPoolMock->expects(static::once())
            ->method('get')
            ->with(TransferFactory::GET_ONE_TIME_USE_TOKEN)
            ->willReturn($commandMock);

        $this->registryMock->expects(static::once())
            ->method('registry')
            ->with(TransactionGetOTUT::NEXIO_ONE_TIME_USE_TOKEN_KEY)
            ->willReturn($result);


        $request = array(
            'billingAddress' => array(
                                        'ordernumber' => 123
            ),
            'totals' => array(
                'amount' => 5.99
            )
        );

        $data = json_encode($request);


        self::assertEquals($this->IframeConfig->process($request,$resultMock), $result);  
    }

    public function testProcess_fail()
    {
        
        $result = false;
        

        $resultMock = $this->getResultMock();
        $resultMock->method('setData')
            ->with($result)
            ->willReturn($result);

        $this->resultFactoryMock->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($resultMock);


        $request = array(
            'billingAddress' => array(
                                        'ordernumber' => 123
            ),
            //'totals' => array(
            //    'amount' => 5.99
            //)
        );

        $data = json_encode($request);


        self::assertEquals($this->IframeConfig->process($request,$resultMock), $result);  


        $request = array(
            //'billingAddress' => array(
            //                            'ordernumber' => 123
            //),
            'totals' => array(
                'amount' => 5.99
            )
        );

        $data = json_encode($request);


        self::assertEquals($this->IframeConfig->process($request,$resultMock), $result);  

        self::assertEquals($this->IframeConfig->process(null,$resultMock), $result); 
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



