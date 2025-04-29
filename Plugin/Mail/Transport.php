<?php

namespace SelectCo\Mailgun\Plugin\Mail;

use SelectCo\Mailgun\Helper\Data;
use Magento\Framework\Mail\TransportInterface;
use SelectCo\Mailgun\Model\Mailgun;

class Transport
{
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var Mailgun
     */
    private $mailgun;

    /**
     * @param Data $helper
     * @param Mailgun $mailgun
     */
    public function __construct(Data $helper, Mailgun $mailgun)
    {
        $this->helper = $helper;
        $this->mailgun = $mailgun;
    }

    /**
     * @param TransportInterface $subject
     * @param callable $proceed
     */
    public function aroundSendMessage(TransportInterface $subject, callable $proceed) {
        if ($this->helper->isEnabled()) {
            $this->mailgun->processMessage($subject->getMessage());
        } else {
            $proceed();
        }
    }
}
