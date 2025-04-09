<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SelectCo\Mailgun\Model;

use Mailgun\HttpClientConfigurator;
use Mailgun\Mailgun as MG;
use Mailgun\Model\Message\SendResponse;
use SelectCo\Mailgun\Helper\Data;

class Mailgun
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
     * @param \Magento\Framework\Mail\MessageInterface|\Magento\Framework\Mail\EmailMessageInterface $message
     * @return SendResponse
     */
    public function processMessage($message)
    {
        try {
            $configurator = new HttpClientConfigurator();

            if ($this->helper->isDebugMode())
            {
                $configurator->setDebug(true);
            }

            if ($this->helper->isPasteBinEnabled() && $this->helper->getPasteBinId() !== null)
            {
                $configurator->setEndpoint($this->helper::PASTEBIN_URL . $this->helper->getPasteBinId());
            }
            else
            {
                $configurator->setEndpoint($this->helper->getEndpoint());
            }
            $configurator->setApiKey($this->helper->getApiKey());

            $mailgun = MG::configure($configurator);

            $params = $this->createMessageParameters($message);
            return $mailgun->messages()->send($this->helper->getDomain(), $params);

        }
        catch (\Exception $e)
        {
            return SendResponse::create(['message' => $e->getMessage()]);
        }
    }

    /**
     * @param \Magento\Framework\Mail\MessageInterface|\Magento\Framework\Mail\EmailMessageInterface $message
     * @return array
     */
    private function createMessageParameters($message): array
    {
        $attachments = [];

        if ($message instanceof \Magento\Framework\Mail\MessageInterface)
        {
            $message = \Zend\Mail\Message::fromString($message->getRawMessage());
            if ($message->getBody() instanceof Mime\Message) {
                /** @var \Zend\Mime\Part $part */
                foreach ($message->getBody()->getParts() as $part) {
                    if ($part->disposition == 'attachment') {
                        $attachments[] = $part;
                    }
                }
            }
        }
        else
        {
            foreach ($message->getMessageBody()->getParts() as $part) {
                if ($part->getDisposition() === 'attachment') {
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