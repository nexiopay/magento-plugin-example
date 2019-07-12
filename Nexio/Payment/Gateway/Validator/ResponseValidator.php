<?php

namespace Nexio\Payment\Gateway\Validator;


/**
 * Class ResponseValidator
 * @package Nexio\Payment\Gateway\Validator
 */
class ResponseValidator implements \Magento\Payment\Gateway\Validator\ValidatorInterface
{
    /**
     * @var \Nexio\Payment\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Payment\Gateway\Validator\ResultInterfaceFactory
     */
    protected $resultFactory;

    /**
     * ResponseValidator constructor.
     * @param \Nexio\Payment\Logger\Logger $logger
     * @param \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultInterfaceFactory
     */
    public function __construct(
        \Nexio\Payment\Logger\Logger $logger,
        \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultInterfaceFactory
    ) {
        $this->logger = $logger;
        $this->resultFactory = $resultInterfaceFactory;
    }

    /**
     * @param array $validationSubject
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     */
    public function validate(array $validationSubject)
    {
        $this->logger->addDebug("begin response validate process...");
        
        return $this->resultFactory->create([
            'isValid' => true,
            'failsDescription' => []
        ]);
    }
}

