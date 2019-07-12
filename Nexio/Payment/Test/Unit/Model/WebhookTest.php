<?php
namespace Nexio\Payment\Test\Unit\Gateway\Request;

use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Nexio\Payment\Model\Webhook;
use Nexio\Payment\Logger\Logger;


class WebhookTest extends \PHPUnit\Framework\TestCase
{

    private $loggerMock;
    
    private $Webhook;
    

    protected function Setup()
    {
        $this->loggerMock = $this->getMockBuilder(\Nexio\Payment\Logger\Logger::class)
        ->disableOriginalConstructor()
        ->getMock();

        $this->loggerMock->expects(self::any())
            ->method('addDebug')
            ->willReturn(true);

        $this->Webhook = $this->getMockBuilder(Webhook::class)
            ->setConstructorArgs([$this->loggerMock])
            ->setMethods(['callGetSecret'])
            ->getMock();

        /*$this->Webhook->expects(self::once())
            ->method('callGetSecret')
            ->willReturn('error');*/
        
    }


    public function test_test()
    {
        $response = array('result'=>'success');

        $headsign = '';
        $post = "{\"eventType\":\"TRANSACTION_CAPTURED\",\"data\":{\"id\":\"eyJuYW1lIjoidXNhZXBheSIsIm1lcmNoYW50SWQiOiIxMDAwMzkiLCJyZWZOdW1iZXIiOiIzMTA0MTgwMjI1IiwicmFuZG9tIjowLCJjdXJyZW5jeSI6InVzZCJ9\",\"merchantId\":\"100039\",\"transactionDate\":\"2019-07-12T02:10:01.110Z\",\"authCode\":\"689132\",\"transactionStatus\":\"pending\",\"amount\":5.99,\"transactionType\":\"sale\",\"currency\":\"USD\",\"gatewayResponse\":{\"result\":\"Approved\",\"batchRef\":\"410194\",\"refNumber\":\"3104180225\",\"gatewayName\":\"usaepay\",\"message\":\"Approved\"},\"data\":{\"amount\":5.99,\"currency\":\"USD\",\"settlementCurrency\":\"USD\",\"customer\":{\"firstName\":\"Sam\",\"lastName\":\"Lu\",\"orderNumber\":\"137\",\"billToAddressOne\":\"815 West University Parkway\",\"billToCountry\":\"US\",\"billToPostal\":\"84058\",\"billToState\":\"UT\",\"billToCity\":\"Orem\"}},\"card\":{\"cardNumber\":\"424242******4242\",\"expirationYear\":\"2019\",\"expirationMonth\":\"10\",\"cardHolder\":\"Sam Lu\"},\"kountResponse\":{\"status\":\"success\"},\"token\":{\"firstSix\":\"424242\",\"lastFour\":\"4242\",\"success\":true,\"error\":\"\",\"cardType\":\"visa\",\"sesssionID\":\"83f4a0dc7376439a82b64b0b1d601fb\",\"customerRefNumber\":\"06b02940-682e-458c-8a00-f\"}}}";

        $this->Webhook->process($headsign,$post);
       
    }

}


