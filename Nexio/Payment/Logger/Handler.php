<?php

namespace Nexio\Payment\Logger;

use Monolog\Logger as MonoLogger;

/**
 * Class Handler
 * @package Nexio\Payment\Logger
 */
class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = MonoLogger::DEBUG;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/nexio/debug.log';

    /**
     * Handler constructor.
     * @param \Magento\Framework\Filesystem\Driver\File $filesystem
     * @param null $filePath
     */
    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $filesystem,
        $filePath = null
    )
    {
        parent::__construct($filesystem, $filePath);
    }
}
