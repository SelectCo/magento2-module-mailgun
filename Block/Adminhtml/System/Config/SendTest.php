<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SelectCo\Mailgun\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class SendTest extends Field
{
    /**
     * @var string
     */
    protected $_template = 'SelectCo_Mailgun::system/config/button.phtml';

    /**
     * Send Test Email Button Label
     *
     * @var string
     */
    protected $_testButtonLabel = 'Send Test';

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $button = $this->getLayout()->createBlock(
            Button::class
        )->setData([
            'id' => $element->getHtmlId(),
            'label' => __($this->_testButtonLabel),
            'class' => 'primary'
        ]);

        $this->addData(
            [
                'button_html' => $button->toHtml(),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl('selectco_mailgun/system_config_mailgun/test'),
            ]
        );

        return $this->_toHtml();
    }
}
