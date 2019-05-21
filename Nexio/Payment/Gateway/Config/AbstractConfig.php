<?php

namespace Nexio\Payment\Gateway\Config;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Config\ValueHandlerInterface;

/**
 * Class AbstractConfig
 * @package Nexio\Payment\Gateway\Config
 */
abstract class AbstractConfig implements ValueHandlerInterface
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var string
     */
    protected $configKey;

    /**
     * AbstractConfig constructor.
     * @param ConfigInterface $config
     * @param string $key
     */
    public function __construct(ConfigInterface $config, $key)
    {
        $this->config = $config;
        $this->configKey = $key;
    }

    /**
     * @param array $subject
     * @param null $storeId
     * @return mixed
     */
    public function handle(array $subject, $storeId = null)
    {
        return $this->config->getValue($this->configKey, $storeId);
    }
}
