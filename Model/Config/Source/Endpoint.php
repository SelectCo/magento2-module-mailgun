<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SelectCo\Mailgun\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Endpoint implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'https://api.mailgun.net/', 'label' => __('https://api.mailgun.net/ (US Region)')],
            ['value' => 'https://api.eu.mailgun.net/', 'label' => __('https://api.eu.mailgun.net/ (EU Region)')]
        ];
    }
}
