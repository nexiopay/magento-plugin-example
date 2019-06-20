<?php
namespace Nexio\Payment\Test\Unit\Gateway\Request;

use Magento\Vault\Api\PaymentTokenManagementInterface; 
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Nexio\Payment\Gateway\Request\GetOneTimeUseTokenDataBuilder;


class GetOneTimeUseTokenDataBuilderTest extends \PHPUnit\Framework\TestCase
{
    private $PaymentTokenManagementMock;
    private $ConfigMock;
    private $GOTUTDB;

    protected function Setup()
    {
        $this->PaymentTokenManagementMock = $this->getMockBuilder(PaymentTokenManagementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->ConfigMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->GOTUTDB = new GetOneTimeUseTokenDataBuilder($this->ConfigMock, $this->PaymentTokenManagementMock);
        
    }


    public function test_test()
    {
        
        $param = array(
            'billingAddress' => array(
                'ordernumber' => 1,
                'firstname' => 'Sam',
                'lastname' => 'Lu',
                'street1' => 'No. 653 Test Street ',
                'street2' => '',
                'city' => 'Test City',
                'regionCode' => '01',
                'postcode' => '200001',
                'countryId' => '86'
            ),
            'totals' => array(
                'base_currency_code' => '01',
                'base_grand_total' => '5.99'
            ),
        );

        $_SERVER['HTTP_HOST'] = 'mag.cmsshanghaidev.com';

        $expected = array(
            'data' => array(
                'paymentMethod' => 'creditCard',
                'currency'=>'01',
                'amount' => '5.99',
                'partialAmount' => '5.99',
                'description'=>'DES',
                'allowedCardTypes' => ["visa","mastercard","discover","amex"],
                'customer' => array(
                    'orderNumber'=>1,
                    'firstName'=>'Sam',
                    'lastName' => 'Lu',
                    'billToAddressOne' => 'No. 653 Test Street ',
                    'billToAddressTwo' => '',
                    'billToCity'=>'Test City',
                    'billToState'=> '01',
                    'billToPostal' => '200001',
                    'billToCountry' => '86'
                )
            ),
            'processingOptions' => array(
                'webhookUrl' => "https://".$_SERVER['HTTP_HOST']."/rest/V1/hello/test",
                'checkFraud'=>false,
                'verboseResponse' => false,
                'saveCardToken' => false
            ),
            'uiOptions' => array(
                'css' => '',
                'displaySubmitButton' => false,
                'hideCvc' => false,
                'requireCvc' => false,
                'hideBilling' => false,
                'customTextUrl' => ''
            ),
            'card' => array(
                'cardHolderName' => 'Sam Lu'
            ),
            'isAuthOnly' => false
        );

        self::assertEquals($this->GOTUTDB->build($param),$expected);
        //fwrite(STDERR, print_r('||'.json_encode($this->GOTUTDB->build($param)).'||', TRUE));    
    }

}

