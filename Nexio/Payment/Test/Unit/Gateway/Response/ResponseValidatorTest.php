<?php
namespace Nexio\Payment\Test\Unit\Gateway\Request;

use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Nexio\Payment\Gateway\Validator\ResponseValidator;
use Nexio\Payment\Logger\Logger;


class ResponseValidatorTest extends \PHPUnit\Framework\TestCase
{

    private $loggerMock;
    private $ResultInterfaceFactoryMock;
    private $RV;
    

    protected function Setup()
    {
        $this->loggerMock = $this->getMockBuilder(\Nexio\Payment\Logger\Logger::class)
        ->disableOriginalConstructor()
        ->getMock();

        $this->ResultInterfaceFactoryMock = $this->getMockBuilder(ResultInterfaceFactory::class)
                                ->disableOriginalConstructor()
                                ->getMock();


        $this->loggerMock->expects(self::once())
            ->method('addDebug')
            ->willReturn(true);

        $this->RV = new ResponseValidator($this->loggerMock,$this->ResultInterfaceFactoryMock);
        
    }


    public function test_test()
    {
        $response = array('result'=>'success');

       
        $this->RV->validate($response);
       
    }

}


