<?php
namespace Nexio\Payment\Test\Unit\Gateway\Request;

use Magento\Vault\Api\PaymentTokenManagementInterface; 
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;
use Nexio\Payment\Gateway\Response\PaymentDetailsHandler;
use Nexio\Payment\Logger\Logger;

class PaymentDetailsHandlerTest extends \PHPUnit\Framework\TestCase
{
    private $PaymentTokenManagementMock;
    private $ConfigMock;
    private $orderMock;
    

    private $PaymentDetailsHandlerMock;
    private $loggerMock;
    private $paymentDO;
    private $payment;

    protected function Setup()
    {
        $this->loggerMock = $this->getMockBuilder(\Nexio\Payment\Logger\Logger::class)
        ->disableOriginalConstructor()
        ->getMock();

        $this->paymentDO = $this->getMockBuilder(PaymentDataObjectInterface::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $this->orderMock = $this->getMockBuilder(OrderAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock->expects(self::exactly(2))
            ->method('addDebug')
            ->willReturn(true);

        $this->payment = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->paymentDO->expects(self::any())
            ->method('getOrder')
            ->willReturn($this->orderMock);
        $this->paymentDO->expects(self::any())
            ->method('getPayment')
            ->willReturn($this->payment);

        $this->payment->expects(self::any())
            ->method('getAdditionalInformation')
            ->withConsecutive(['token'],  ['expMonth'], ['expYear'])
            ->willReturnOnConsecutiveCalls('testtoken', '12','2030');   

        $this->orderMock->expects(self::any())
            ->method('getCustomerId')
            ->willReturn('0001');    

        $this->PaymentDetailsHandlerMock = new PaymentDetailsHandler($this->loggerMock);
        
    }


    public function test_test()
    {
        $response = array('result'=>'success');

       
        $this->PaymentDetailsHandlerMock->handle(['payment' => $this->paymentDO],$response);
       
    }

}


