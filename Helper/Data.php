<?php

namespace SelectCo\Mailgun\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use SelectCo\Core\Helper\Data as CoreHelper;

class Data extends AbstractHelper
{
    const MODULE_ENABLED = 'selectco_mailgun/general/enabled';
    const SENDER_DOMAIN = 'selectco_mailgun/api/sender_domain';
    const API_KEY = 'selectco_mailgun/api/api_key';
    const MAILGUN_ENDPOINT = 'selectco_mailgun/api/endpoint';
    const MAILGUN_DEBUG_MODE = 'selectco_mailgun/debug/enabled';
    const MAILGUN_PASTEBIN_ENABLED = 'selectco_mailgun/debug/pastebin_enabled';
    const MAILGUN_PASTEBIN_ID = 'selectco_mailgun/debug/pastebin_id';
    const PASTEBIN_URL = 'http://bin.mailgun.net/';

    /**
     * @var CoreHelper
     */
    private $coreHelper;


    /**
     * @param Context $context
     * @param CoreHelper $coreHelper
     */
    public function __construct(Context $context, CoreHelper $coreHelper)
    {
        parent::__construct($context);

        $this->coreHelper = $coreHelper;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->coreHelper->getConfigValue(self::MODULE_ENABLED);
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->coreHelper->getConfigValue(self::SENDER_DOMAIN);
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->coreHelper->getConfigValue(self::API_KEY);
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->coreHelper->getConfigValue(self::MAILGUN_ENDPOINT);
    }

    /**
     * @return bool
     */
    public function isDebugMode(): bool
    {
        return (bool)$this->coreHelper->getConfigValue(self::MAILGUN_DEBUG_MODE);
    }

    /**
     * @return bool
     */
    public function isPasteBinEnabled(): bool
    {
        return (bool)$this->coreHelper->getConfigValue(self::MAILGUN_PASTEBIN_ENABLED);
    }

    /**
     * @return string
     */
    public function getPasteBinId(): string
    {
        return $this->coreHelper->getConfigValue(self::MAILGUN_PASTEBIN_ID);
    }
}
