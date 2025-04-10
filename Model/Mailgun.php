<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SelectCo\Mailgun\Model;

use Exception;
use Mailgun\HttpClientConfigurator;
use Mailgun\Mailgun as MG;
use Mailgun\Model\Message\SendResponse;
use SelectCo\Mailgun\Helper\Data;
use TypeError;

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
        catch (Exception $e)
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
        $messageBody = '';

        foreach ($message->getMessageBody()->getParts() as $part) {
            if ($part->getType() === 'text/html') {
                $messageBody = $part->getContent();
            }
            try {
                if ($part->getDisposition() === 'attachment') {
                    $attachments[] = [
                        'fileContent' => $part->getRawContent(),
                        'filename'=>$part->getFileName()
                    ];
                }
            } catch (TypeError $e) {}
        }

        return [
            'to' => $this->parseAddressList($message->getTo()),
            'cc' => $this->parseAddressList($message->getCc()),
            'bcc' => $this->parseAddressList($message->getBcc()),
            'from' => $this->parseAddressList($message->getFrom()),
            'reply-to' => $this->parseAddressList($message->getReplyTo()),
            'subject' => $message->getSubject(),
            'html' => quoted_printable_decode($messageBody),
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