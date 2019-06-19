<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Nexio\Payment\Test\Unit\Controller\Checkout;

//Abstract Controller needs
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Nexio\Payment\Gateway\Http\TransferFactory;
use Nexio\Payment\Gateway\Http\Client\TransactionGetOneTimeUseToken as TransactionGetOTUT;
use Magento\Framework\Controller\ResultFactory;


use Magento\Framework\Registry;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\DB\TransactionFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Transaction;

use Nexio\Payment\Controller\Checkout\GetSecretConfig;
use Nexio\Payment\Logger\Logger;

class GetSecretConfigTest extends \PHPUnit\Framework\TestCase
{

    private $orderPlaceMock;

    private $configMock;

    private $checkoutSessionMock;

    private $requestMock;

    private $resultFactoryMock;

    protected $messageManagerMock;

    private $GetSecretConfig;

    private $loggerMock;

    private $customerSessionMock;

    private $commandPoolMock;

    private $registryMock;

    private $encryptorMock;

    private $orderFactoryMock;

    private $transactionFactoryMock;


    protected function setUp()
    {
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(\Nexio\Payment\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
       
        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
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
            ->setMethods(['load','create'])
            ->getMock();
        
        $this->transactionFactoryMock = $this->getMockBuilder(TransactionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
            

        $contextMock->expects(self::any())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);    

            /*
        $this->GetSecretConfig = new GetSecretConfig(
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
        );*/
    }

    /*
    public function test_getsecret()
    {
        //$_GET["command"] = 'it is a test';

        $this->configMock->expects(self::any())
            ->method('getValue')
            ->withConsecutive(['merchant_id'], ['verify_signature'])
            ->willReturnOnConsecutiveCalls(100039, true);   

        $this->GetSecretConfig->execute();
        self::assertEquals(true,true);
    }*/


    public function test_getsecret_verifysig_false()
    {
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects(self::any())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock); 

        $configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $getSecretMock = $this->getMockBuilder(GetSecretConfig::class)
        ->setConstructorArgs([$contextMock,
                                $configMock,
                                $this->customerSessionMock,
                                $this->commandPoolMock,
                                $this->loggerMock,
                                $this->registryMock,
                                $this->encryptorMock,
                                $this->checkoutSessionMock,
                                $this->orderFactoryMock,
                                $this->transactionFactoryMock])
        ->setMethods(['get_secret'])
        ->getMock();
        
        $configMock->expects(self::any())
            ->method('getValue')
            ->withConsecutive(['merchant_id'], ['verify_signature'])
            ->willReturnOnConsecutiveCalls(100039, false);   

        $response = array(
                'verifyflag' =>false,
                'secret' => 'error'
            );
        $this->expectOutputString(json_encode($response));
        $getSecretMock->execute();

    }

    public function test_mockfunction()
    {
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects(self::any())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);    
        $configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $getSecretMock = $this->getMockBuilder(GetSecretConfig::class)
        ->setConstructorArgs([$contextMock,
                                $configMock,
                                $this->customerSessionMock,
                                $this->commandPoolMock,
                                $this->loggerMock,
                                $this->registryMock,
                                $this->encryptorMock,
                                $this->checkoutSessionMock,
                                $this->orderFactoryMock,
                                $this->transactionFactoryMock])
        ->setMethods(['get_secret'])
        ->getMock();

        $configMock->expects(self::any())
            ->method('getValue')
            ->withConsecutive(['merchant_id'], ['verify_signature'])
            ->willReturnOnConsecutiveCalls(100039, true);   

        $getSecretMock->method('get_secret')
        ->with(100039)
        ->willReturn('testsecret');

        $response = array(
            'verifyflag' =>true,
            'secret' => 'testsecret'
        );
        $this->expectOutputString(json_encode($response));
        $getSecretMock->execute();

    }

    public function test_updatesecret()
    {
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects(self::any())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);    
        $configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $getSecretMock = $this->getMockBuilder(GetSecretConfig::class)
        ->setConstructorArgs([$contextMock,
                                $configMock,
                                $this->customerSessionMock,
                                $this->commandPoolMock,
                                $this->loggerMock,
                                $this->registryMock,
                                $this->encryptorMock,
                                $this->checkoutSessionMock,
                                $this->orderFactoryMock,
                                $this->transactionFactoryMock])
        ->setMethods(['get_secret','update_secret'])
        ->getMock();

        $configMock->expects(self::any())
            ->method('getValue')
            ->withConsecutive(['merchant_id'], ['verify_signature'])
            ->willReturnOnConsecutiveCalls(100039, true);   

        $getSecretMock->method('get_secret')
        ->with(100039)
        ->willReturn('error');

        $getSecretMock->method('update_secret')
        ->with(100039)
        ->willReturn('updatesecret');

        $response = array(
            'verifyflag' =>true,
            'secret' => 'updatesecret'
        );
        $this->expectOutputString(json_encode($response));
        $getSecretMock->execute();

    }
    
    
    public function test_updateorderwitherr()
    {
        $_GET["command"] = 'updateorderwitherr';
        $_GET["orderId"] = '1';
        $_GET["msg"] = 'test error';
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects(self::any())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);    
        $configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $getSecretMock = $this->getMockBuilder(GetSecretConfig::class)
        ->setConstructorArgs([$contextMock,
                                $configMock,
                                $this->customerSessionMock,
                                $this->commandPoolMock,
                                $this->loggerMock,
                                $this->registryMock,
                                $this->encryptorMock,
                                $this->checkoutSessionMock,
                                $this->orderFactoryMock,
                                $this->transactionFactoryMock])
        ->setMethods(['get_secret','update_secret'])
        ->getMock();

        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['addStatusHistoryComment','save'])
            ->getMock();

        
        $orderMock->expects(self::once())
            ->method('addStatusHistoryComment')
            ->with($_GET["msg"])
            ->willReturn(true);
        
        $orderMock->expects(self::once())
            ->method('save')
            ->willReturn(true);
        

        $this->orderFactoryMock->expects(self::once())
            ->method('load')
            ->willReturn($orderMock);

        $this->orderFactoryMock->expects(self::once())
            ->method('create')
            ->willReturn($this->orderFactoryMock);

        $getSecretMock->execute();
    }

    
    public function test_updateorder()
    {
        $_GET["command"] = 'updateorder';
        $_GET["orderId"] = '1';
        $_GET["amount"] = '5.99';
        $_GET["authCode"]= '1221'; 
        $_GET["eventType"] = 'TRANSACTION_CAPTURED';
        $_GET["verifybypass"] = false ;
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects(self::any())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);    
        $configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $getSecretMock = $this->getMockBuilder(GetSecretConfig::class)
        ->setConstructorArgs([$contextMock,
                                $configMock,
                                $this->customerSessionMock,
                                $this->commandPoolMock,
                                $this->loggerMock,
                                $this->registryMock,
                                $this->encryptorMock,
                                $this->checkoutSessionMock,
                                $this->orderFactoryMock,
                                $this->transactionFactoryMock])
        ->setMethods(['get_secret','update_secret'])
        ->getMock();

        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['addStatusHistoryComment','save','getPayment','prepareInvoice'])
            ->getMock();

        //need judge how many times 'addStatusHistoryComment' method is called
        /*
        $orderMock->expects(self::once())
            ->method('addStatusHistoryComment')
            ->withConsecutive(['Webhook function signature verification passed!'], ['Nexio AuthCode is: '.$_GET["authCode"]])
            ->willReturnOnConsecutiveCalls(true, true);
        */

        $orderMock->expects(self::once())
            ->method('save')
            ->willReturn(true);

        $this->orderFactoryMock->expects(self::once())
            ->method('load')
            ->willReturn($orderMock);

        $this->orderFactoryMock->expects(self::once())
            ->method('create')
            ->willReturn($this->orderFactoryMock);

        $paymentMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->setMethods(['setShouldCloseParentTransaction','setIsTransactionClosed'])
            ->getMock();

        $paymentMock->expects(self::once())
            ->method('setShouldCloseParentTransaction')
            ->willReturn(true);
        
        $paymentMock->expects(self::once())
            ->method('setIsTransactionClosed')
            ->willReturn(true);

        $invoiceMock = $this->getMockBuilder(Invoice::class)
            ->disableOriginalConstructor()
            ->setMethods(['setRequestedCaptureCase','register','save'])
            ->getMock();
        
        $transactionMock = $this->getMockBuilder(Transaction::class)
            ->disableOriginalConstructor()
            ->setMethods(['addObject','save'])
            ->getMock();

        $orderMock->expects(self::once())
            ->method('getPayment')
            ->willReturn($paymentMock);
        
        $orderMock->expects(self::once())
            ->method('prepareInvoice')
            ->willReturn($invoiceMock);



        $this->orderFactoryMock->expects(self::once())
            ->method('load')
            ->willReturn($orderMock);

        $this->transactionFactoryMock->expects(self::once())
            ->method('create')
            ->willReturn($transactionMock);    
            

        $getSecretMock->execute();
    }

    public function test_updateorder_verifybypassed()
    {
        $_GET["command"] = 'updateorder';
        $_GET["orderId"] = '1';
        $_GET["amount"] = '5.99';
        $_GET["authCode"]= '1221'; 
        $_GET["eventType"] = 'TRANSACTION_CAPTURED';
        $_GET["verifybypass"] = true ;
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects(self::any())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);    
        $configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $getSecretMock = $this->getMockBuilder(GetSecretConfig::class)
        ->setConstructorArgs([$contextMock,
                                $configMock,
                                $this->customerSessionMock,
                                $this->commandPoolMock,
                                $this->loggerMock,
                                $this->registryMock,
                                $this->encryptorMock,
                                $this->checkoutSessionMock,
                                $this->orderFactoryMock,
                                $this->transactionFactoryMock])
        ->setMethods(['get_secret','update_secret'])
        ->getMock();

        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['addStatusHistoryComment','save','getPayment','prepareInvoice'])
            ->getMock();

        //need judge how many times 'addStatusHistoryComment' method is called
        /*
        $orderMock->expects(self::once())
            ->method('addStatusHistoryComment')
            ->withConsecutive(['Webhook function signature verification passed!'], ['Nexio AuthCode is: '.$_GET["authCode"]])
            ->willReturnOnConsecutiveCalls(true, true);
        */

        $orderMock->expects(self::once())
            ->method('save')
            ->willReturn(true);

        $this->orderFactoryMock->expects(self::once())
            ->method('load')
            ->willReturn($orderMock);

        $this->orderFactoryMock->expects(self::once())
            ->method('create')
            ->willReturn($this->orderFactoryMock);

        $paymentMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->setMethods(['setShouldCloseParentTransaction','setIsTransactionClosed'])
            ->getMock();

        $paymentMock->expects(self::once())
            ->method('setShouldCloseParentTransaction')
            ->willReturn(true);
        
        $paymentMock->expects(self::once())
            ->method('setIsTransactionClosed')
            ->willReturn(true);

        $invoiceMock = $this->getMockBuilder(Invoice::class)
            ->disableOriginalConstructor()
            ->setMethods(['setRequestedCaptureCase','register','save'])
            ->getMock();
        
        $transactionMock = $this->getMockBuilder(Transaction::class)
            ->disableOriginalConstructor()
            ->setMethods(['addObject','save'])
            ->getMock();

        $orderMock->expects(self::once())
            ->method('getPayment')
            ->willReturn($paymentMock);
        
        $orderMock->expects(self::once())
            ->method('prepareInvoice')
            ->willReturn($invoiceMock);



        $this->orderFactoryMock->expects(self::once())
            ->method('load')
            ->willReturn($orderMock);

        $this->transactionFactoryMock->expects(self::once())
            ->method('create')
            ->willReturn($transactionMock);    
            

        $getSecretMock->execute();
    }

    public function test_updateorder_authonly()
    {
        $_GET["command"] = 'updateorder';
        $_GET["orderId"] = '1';
        $_GET["amount"] = '5.99';
        $_GET["authCode"]= '1221'; 
        $_GET["eventType"] = 'TRANSACTION_AUTHORIZED';
        $_GET["verifybypass"] = false ;
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects(self::any())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);    
        $configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $getSecretMock = $this->getMockBuilder(GetSecretConfig::class)
        ->setConstructorArgs([$contextMock,
                                $configMock,
                                $this->customerSessionMock,
                                $this->commandPoolMock,
                                $this->loggerMock,
                                $this->registryMock,
                                $this->encryptorMock,
                                $this->checkoutSessionMock,
                                $this->orderFactoryMock,
                                $this->transactionFactoryMock])
        ->setMethods(['get_secret','update_secret'])
        ->getMock();

        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['addStatusHistoryComment','save','getPayment','prepareInvoice'])
            ->getMock();

        //need judge how many times 'addStatusHistoryComment' method is called
        /*
        $orderMock->expects(self::once())
            ->method('addStatusHistoryComment')
            ->withConsecutive(['Webhook function signature verification passed!'], ['Nexio AuthCode is: '.$_GET["authCode"]])
            ->willReturnOnConsecutiveCalls(true, true);
        */

        $orderMock->expects(self::once())
            ->method('save')
            ->willReturn(true);

        $this->orderFactoryMock->expects(self::once())
            ->method('load')
            ->willReturn($orderMock);

        $this->orderFactoryMock->expects(self::once())
            ->method('create')
            ->willReturn($this->orderFactoryMock);

        $paymentMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->setMethods(['setShouldCloseParentTransaction','setIsTransactionClosed'])
            ->getMock();

        $paymentMock->expects(self::once())
            ->method('setShouldCloseParentTransaction')
            ->willReturn(true);
        
        $paymentMock->expects(self::once())
            ->method('setIsTransactionClosed')
            ->willReturn(true);


        $orderMock->expects(self::once())
            ->method('getPayment')
            ->willReturn($paymentMock);

        $this->orderFactoryMock->expects(self::once())
            ->method('load')
            ->willReturn($orderMock);
  

        $getSecretMock->execute();
    }
}
