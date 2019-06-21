<?php
namespace Nexio\Payment\Webapi;
 
interface WebhookInterface
{
    /**
     * success_callback
     * @api
     * @return none
     */
    public function success();

    /**
     * loadsecret
     *
     * @api
     * @return none
     */
    public function loadsecret();

}

