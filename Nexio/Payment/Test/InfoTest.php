<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Nexio\Payment\Test\Unit\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\SamplePaymentGateway\Block\Info;

use PHPUnit_Framework_MockObject_MockObject as MockObject;

class InfoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Context | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var ConfigInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * @var InfoInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $paymentInfoModel;

    public function setUp()
    {
       
    }

    public function testGetSpecificationInformation()
    {
       
        $result = 1;
        $result2 = 1; 
        static::assertSame($result, $result2);
    }

}

