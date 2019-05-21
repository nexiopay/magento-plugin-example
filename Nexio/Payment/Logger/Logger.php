<?php

namespace Nexio\Payment\Logger;

/**
 * Class Logger
 * @package Nexio\Payment\Logger
 */
class Logger extends \Monolog\Logger
{
    /**
     * Logger constructor.
     * @param Handler $handler
     * @param string $name
     * @param array $handlers
     * @param array $processors
     */
    public function __construct(
        \Nexio\Payment\Logger\Handler $handler,
        $name = "NexioPayment",
        $handlers = array(),
        $processors = array()
    )
    {
        parent::__construct($name, $handlers, $processors);
//        $this->handlers = ['system' => $handler];
        $this->pushHandler($handler);
    }
}
