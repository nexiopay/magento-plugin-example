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
        /*
        $fails = [];

        if (@$validationSubject['response']['status_code'] === 200
            && @$validationSubject['response']['body']['gatewayResponse']['result'] === 'Approved') {
            $isValid = true;
        } else {
            $isValid = false;
            $phrase = @$validationSubject['response']['phrase'];
            $reponseMessage = @$validationSubject['response']['body']['gatewayResponse']['message'];
            $this->logger->addDebug(
                "Error calling API: \n Phrase: {$phrase} \n Reponse message: {$reponseMessage}"
            );
            $fails[] = __($phrase);
            $fails[] = __($reponseMessage);
        }

        return $this->resultFactory->create([
            'isValid' => $isValid,
            'failsDescription' => $fails
        ]);
        */
        return $this->resultFactory->create([
            'isValid' => true,
            'failsDescription' => []
        ]);
    }
}
