<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SelectCo\Mailgun\Controller\Adminhtml\System\Config\Mailgun;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use SelectCo\Mailgun\Model\MailgunTest;

class Test extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var MailgunTest
     */
    private $mailgunTest;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Customer::manage';

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param MailgunTest $mailgunTest
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        MailgunTest $mailgunTest
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->mailgunTest = $mailgunTest;
    }

    /**
     * Execute action based on request and return result
     *
     * @return Json
     */
    public function execute()
    {
        $resultData = [
            'valid' => 0,
            'to' => NULL,
            'from' => NULL
        ];

        if (!filter_var($this->getRequest()->getParam('from'), FILTER_VALIDATE_EMAIL))
        {
            $resultData['message'] = 'Invalid from email address';
        }
        else if (!filter_var($this->getRequest()->getParam('to'), FILTER_VALIDATE_EMAIL))
        {
            $resultData['message'] = 'Invalid to email address';
        }
        else
        {
            $result = $this->mailgunTest->send(
                $this->getRequest()->getParam('from'),
                $this->getRequest()->getParam('to')
            );

            $resultData['valid'] = (int)$result->getIsValid();
            $resultData['message'] = $result->getRequestMessage();
            $resultData['from'] = $result->getFrom();
            $resultData['to'] = $result->getTo();
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($resultData);
    }
}
