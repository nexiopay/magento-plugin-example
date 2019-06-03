<?php
namespace Nexio\Payment\Webapi;


interface HelloInterface
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
     * test
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    public function test();

    /**
     * loadsecret
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    public function loadsecret();

}
