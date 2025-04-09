<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SelectCo\Mailgun\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Mail\Template\TransportBuilder;

class MailgunTest
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var Mailgun
     */
    private $mailgunModel;

    public function __construct(TransportBuilder $transportBuilder, Mailgun $mailgunModel)
    {
        $this->mailgunModel = $mailgunModel;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @return DataObject
     */
    public function send($from, $to)
    {
        $gatewayResponse = new DataObject([
            'is_valid' => false,
            'from' => $from,
            'to' => $to,
            'request_message' => __('An error occurred while creating a test message.'),
        ]);

        $transport = $this->transportBuilder
            ->setTemplateIdentifier('selectco_mailgun_test_email')
            ->setTemplateVars(['email_from' => $from, 'email_to' => $to])
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->addTo($to)
            ->setFromByScope(['name' => 'Test Sender', 'email' => $from]);

        $response = $this->mailgunModel->processMessage($transport->getTransport()->getMessage());

        if ($response)
        {
            if ($response->getId())
            {
                $gatewayResponse['is_valid'] = true;
            }
            $gatewayResponse['request_message'] = __($response->getMessage());
        }

        return $gatewayResponse;
    }
}
