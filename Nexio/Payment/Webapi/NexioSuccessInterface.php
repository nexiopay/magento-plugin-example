<?php
namespace Nexio\Payment\Webapi;
 
interface NexioSuccessInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    public function name($name);

    /**
     *  
     * @param \Nexio\Payment\Webapi\Data\NexioSuccessInterface $data
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function SuccessCallback($data);
}
