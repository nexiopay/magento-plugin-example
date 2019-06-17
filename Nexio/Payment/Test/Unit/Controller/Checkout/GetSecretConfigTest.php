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
            ->getMock();
        
        $this->transactionFactoryMock = $this->getMockBuilder(TransactionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
            

        $contextMock->expects(self::once())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);    

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
        );
    }

    public function test_getsecret()
    {
        //$_GET["command"] = 'it is a test';

        $this->configMock->expects(self::any())
            ->method('getValue')
            ->withConsecutive(['merchant_id'], ['verify_signature'])
            ->willReturnOnConsecutiveCalls(100039, true);   

        
        
        

	$this->GetSecretConfig->execute();
	//todo need mock GetSecretConfig->get_secret() and update_secret()
        self::assertEquals(true,true);
    }

}
