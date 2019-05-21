<?php

namespace Nexio\Payment\Gateway\Response;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Vault\Model\Ui\VaultConfigProvider;

/**
 * Class VaultDetailsHandler
 * @package Nexio\Payment\Gateway\Response
 */
class VaultDetailsHandler extends AbstractHandler
{
    /**
     * @var \Nexio\Payment\Gateway\Config\IsVaultEnabled
     */
    protected $isVaultEnabled;

    /**
     * @var \Magento\Vault\Model\CreditCardTokenFactory
     */
    protected $paymentTokenFactory;

    /**
     * @var \Magento\Sales\Api\Data\OrderPaymentExtensionInterfaceFactory
     */
    protected $paymentExtensionFactory;

    /**
     * VaultDetailsHandler constructor.
     * @param \Nexio\Payment\Logger\Logger $logger
     * @param \Nexio\Payment\Gateway\Config\IsVaultEnabled $isVaultEnabled
     * @param \Magento\Vault\Model\CreditCardTokenFactory $cardTokenFactory
     * @param \Magento\Sales\Api\Data\OrderPaymentExtensionInterfaceFactory $extensionInterfaceFactory
     */
    public function __construct(
        \Nexio\Payment\Logger\Logger $logger,
        \Nexio\Payment\Gateway\Config\IsVaultEnabled $isVaultEnabled,
        \Magento\Vault\Model\CreditCardTokenFactory $cardTokenFactory,
        \Magento\Sales\Api\Data\OrderPaymentExtensionInterfaceFactory $extensionInterfaceFactory

    ) {
        parent::__construct($logger);
        $this->isVaultEnabled = $isVaultEnabled;
        $this->paymentTokenFactory = $cardTokenFactory;
        $this->paymentExtensionFactory = $extensionInterfaceFactory;

    }

    /**
     * @param array $handlingSubject
     * @param array $response
     * @throws \Exception
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = SubjectReader::readPayment($handlingSubject);
        if (!$this->isVaultEnable() || $this->isPaidByVault($paymentDO) || !$this->isTokenEnable($paymentDO)) {
            return;
        }
        $payment = $paymentDO->getPayment();

        $token = $payment->getAdditionalInformation('token');
        $expMonth = $payment->getAdditionalInformation('expMonth');
        $expYear = $payment->getAdditionalInformation('expYear');
        $last4 = $payment->getAdditionalInformation('last4');
        $first6 = $payment->getAdditionalInformation('first6');
        $cardType = $this->getCcType($payment->getAdditionalInformation('card_type'));

        $paymentToken = $this->paymentTokenFactory->create();
        $paymentToken->setIsVisible(true);
        $paymentToken->setGatewayToken($token);
        $paymentToken->setExpiresAt($this->getExpirationDate($expMonth, $expYear));
        $paymentToken->setTokenDetails(json_encode([
            'type' => $cardType,
            'maskedCC' => $last4,
            'expirationDate' => $expMonth . '-' . $expYear,
            'cc_number' => $first6 . 'xxxxxx' . $last4
        ]));

        $extensionAttributes = $this->getExtensionAttributes($payment);
        $extensionAttributes->setVaultPaymentToken($paymentToken);
    }

    /**
     * @param \Magento\Payment\Gateway\Data\PaymentDataObjectInterface $paymentDO
     * @return bool
     */
    private function isTokenEnable(\Magento\Payment\Gateway\Data\PaymentDataObjectInterface $paymentDO)
    {
        return !!$paymentDO->getPayment()->getAdditionalInformation(VaultConfigProvider::IS_ACTIVE_CODE);
    }

    /**
     * @param $type
     * @return mixed
     */
    private function getCcType($type)
    {
        $ccTypes = [
            'visa' => 'VI',
            'masterCard' => 'MC',
            'americanExpress' => 'AE',
            'discover' => 'DI'
        ];
        return @$ccTypes[$type] ?: $type;
    }

    /**
     * @return bool
     */
    private function isVaultEnable()
    {
        return !!$this->isVaultEnabled->handle([]);
    }

    /**
     * @param \Magento\Payment\Gateway\Data\PaymentDataObjectInterface $paymentDO
     * @return bool
     */
    private function isPaidByVault(\Magento\Payment\Gateway\Data\PaymentDataObjectInterface $paymentDO)
    {
        return !!$paymentDO->getPayment()->getAdditionalInformation('public_hash');
    }

    /**
     * @param $expMonth
     * @param $expYear
     * @return string
     * @throws \Exception
     */
    private function getExpirationDate($expMonth, $expYear)
    {
        $expDate = new \DateTime(
            $expYear
            . '-'
            . $expMonth
            . '-'
            . '01'
            . ' '
            . '00:00:00',
            new \DateTimeZone('UTC')
        );
        $expDate->add(new \DateInterval('P1M'));
        return $expDate->format('Y-m-d 00:00:00');
    }

    /**
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @return mixed
     */
    private function getExtensionAttributes(\Magento\Payment\Model\InfoInterface $payment)
    {
        $extensionAttributes = $payment->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->paymentExtensionFactory->create();
            $payment->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }
}
