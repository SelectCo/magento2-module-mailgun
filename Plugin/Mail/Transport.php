<?php

namespace SelectCo\Mailgun\Plugin\Mail;

use SelectCo\Mailgun\Helper\Data;
use Magento\Framework\Mail\TransportInterface;
use Mailgun\HttpClientConfigurator;
use Mailgun\Mailgun;

class Transport
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param TransportInterface $subject
     * @param callable $proceed
     */
    public function aroundSendMessage(TransportInterface $subject, callable $proceed) {
        if ($this->helper->isEnabled()) {
            try {
                $configurator = new HttpClientConfigurator();

                if ($this->helper->isPasteBinEnabled() && $this->helper->getPasteBinId() !== null)
                {
                    $configurator->setEndpoint($this->helper::PASTEBIN_URL . $this->helper->getPasteBinId());
                }
                else
                {
                    $configurator->setEndpoint($this->helper->getEndpoint());
                }
                $configurator->setApiKey($this->helper->getApiKey());

                $mailgun = Mailgun::configure($configurator);
                $message = \Zend\Mail\Message::fromString($subject->getMessage()->getRawMessage());
                $params = $this->createMessageParameters($message);
                $mailgun->messages()->send($this->helper->getDomain(), $params);

            }
            catch (\Exception $e)
            {
            }
        } else {
            $proceed();
        }
    }

    /**
     * @param \Zend\Mail\Message|\Laminas\Mail\Message $message
     * @return array
     */
    private function createMessageParameters($message): array
    {
        $attachments = [];
        if ($message->getBody() instanceof Mime\Message) {
            /** @var \Zend\Mime\Part $part */
            foreach ($message->getBody()->getParts() as $part) {
                if ($part->disposition == 'attachment') {
                    $attachments[] = $part;
                }
            }
        }

        return [
            'to' => $this->parseAddressList($message->getTo()),
            'cc' => $this->parseAddressList($message->getCc()),
            'bcc' => $this->parseAddressList($message->getBcc()),
            'from' => $this->parseAddressList($message->getFrom()),
            'reply-to' => $this->parseAddressList($message->getReplyTo()),
            'subject' => $message->getSubject(),
            'html' => quoted_printable_decode($message->getBody()),
            'attachment' => $attachments
        ];
    }

    /**
     * @param $addresses
     * @return array
     */
    private function parseAddressList($addresses): array
    {
        $list = [];

        foreach ($addresses as $address) {
            $list[] = $address->getEmail();
        }

        return $list;
    }
}
