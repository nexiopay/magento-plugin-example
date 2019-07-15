<?php
namespace Nexio\Payment\Test\Unit\Gateway\Request;

use Magento\Vault\Api\PaymentTokenManagementInterface; 
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;
use Nexio\Payment\Gateway\Request\AuthorizeDataBuilder;


class AuthorizeDataBuilderTest extends \PHPUnit\Framework\TestCase
{
    private $PaymentTokenManagementMock;
    private $ConfigMock;
    private $ADB;
    private $paymentDO;
    private $orderMock;
    private $payment;

    protected function Setup()
    {
        $this->PaymentTokenManagementMock = $this->getMockBuilder(PaymentTokenManagementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->ConfigMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderMock = $this->getMockBuilder(OrderAdapterInterface::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $this->paymentDO = $this->getMockBuilder(PaymentDataObjectInterface::class)
                                ->disableOriginalConstructor()
                                ->getMock();
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
            ->method('getCurrencyCode')
            ->willReturn('USD');
        


        $this->orderMock->expects(self::any())
            ->method('getCustomerId')
            ->willReturn('0001');    

        $this->ADB = new AuthorizeDataBuilder($this->ConfigMock, $this->PaymentTokenManagementMock);
        
    }


    public function test_test()
    {
        $expectation = [
            'isAuthOnly' => true,
            'tokenex' => [
                'token' => "test token value"
            ],
            'data' => [
                'amount' => 0.99,
                'currency' => 'USD'
            ]
        ];

        self::assertEquals(
            $expectation,
            $this->ADB->build(['payment' => $this->paymentDO])
        );
        //self::assertEquals($this->GUDB->build($param),$expected);
        //fwrite(STDERR, print_r('||'.json_encode($this->GOTUTDB->build($param)).'||', TRUE));    
    }

}


