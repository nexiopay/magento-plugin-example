<?php
namespace Nexio\Payment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\UrlInterface;

/**
 * Class ConfigProvider
 */
class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'nexio';

    const CC_VAULT_CODE = 'nexio_vault';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * ConfigProvider constructor.
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $return = [
            'payment' => [
                self::CODE => [
                    'isActive' => true,
                    'getIframeUrl' => $this->getIframeUrl(),
                    'iframeBaseUrl' => $this->baseIframeUrl()
                ]
            ]
        ];
        return $return;
    }

    /**
     * @return mixed|string
     */
    private function baseIframeUrl()
    {
        /** @var \Nexio\Payment\Gateway\Http\TransferFactory $transfer */
        $transfer = ObjectManager::getInstance()->create(
            \Nexio\Payment\Gateway\Http\TransferFactory::class,
            ['action' => \Nexio\Payment\Gateway\Http\TransferFactory::GET_BASE_IFRAME_URL]
        );
        $url = $transfer->getUrl() . '?token=';
        if ($this->isForceIframeUrlSecure()) {
            $url = str_replace('http://', 'https://', $url);
        }
        return $url;
    }

    /**
     * @return string
     */
    private function getIframeUrl()
    {
        return $this->urlBuilder->getUrl(
            'nexio/checkout/iframeConfig',
            ['_secure' => true]);
    }

    /**
     * @return bool
     */
    private function isForceIframeUrlSecure()
    {
        return false;
    }
}
