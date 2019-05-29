<?php
namespace Nexio\Payment\Model;
use Nexio\Payment\Webapi\NexioSuccessInterface;
 
class NexioSuccess implements NexioSuccessInterface
{
    /**
     * @var \Nexio\Payment\Logger\Logger
     */
    protected $logger;
    
    public function __construct(
        \Nexio\Payment\Logger\Logger $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    public function name($name) {
        $this->logger->addDebug('webapi get name: '.$name);
        return "Aloha! Hello, " . $name;
    }

    public function SuccessCallback($data)
    {
        $this->logger->addDebug('enter post data function');
        return 0;
    }
}
