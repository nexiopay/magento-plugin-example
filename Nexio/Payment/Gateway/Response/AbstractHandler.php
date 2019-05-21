<?php

namespace Nexio\Payment\Gateway\Response;


/**
 * Class AbstractHandler
 * @package Nexio\Payment\Gateway\Response
 */
abstract class AbstractHandler implements \Magento\Payment\Gateway\Response\HandlerInterface
{
    /**
     * @var \Nexio\Payment\Logger\Logger
     */
    protected $logger;

    /**
     * AbstractHandler constructor.
     * @param \Nexio\Payment\Logger\Logger $logger
     */
    public function __construct(
        \Nexio\Payment\Logger\Logger $logger
    ) {
        $this->logger = $logger;
    }
}
