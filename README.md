# Magento 2 Mailgun Module
[![Github license](https://img.shields.io/github/license/SelectCo/magento2-module-mailgun "Github license")](https://github.com/SelectCo/magento2-module-mailgun/blob/main/LICENSE)
[![Open issues](https://img.shields.io/github/issues/SelectCo/magento2-module-mailgun "Open issues")](https://github.com/SelectCo/magento2-module-mailgun/issues)
[![Open Pull Requests](https://img.shields.io/github/issues-pr/SelectCo/magento2-module-mailgun "Open Pull Requests")](https://github.com/SelectCo/magento2-module-mailgun/pulls)
[![Last commit](https://img.shields.io/github/last-commit/SelectCo/magento2-module-mailgun "Last commit")](https://github.com/SelectCo/magento2-module-mailgun/commits/main)


Magento 2 module that routes all outgoing Magento emails through the Mailgun HTTP API.

When enabled, the module replaces Magento's default mail transport with a plugin that submits messages to Mailgun, including HTML content and attachments. It also provides an admin configuration section with a built-in "Send Test Email" tool to validate your setup.

## Features
- Send all Magento transactional emails via Mailgun API
- Supports US and EU Mailgun endpoints
- Sends HTML content and attachments
- Optional debug mode and Mailgun Request Bin (Pastebin) integration for inspecting requests
- Admin "Send Test Email" button to quickly verify configuration

## Requirements
- Magento 2 (tested with 2.3.5)
- A Mailgun account with a verified sending domain
- PHP version compatible with your Magento installation
- Composer dependencies (handled via this module):
  - mailgun/mailgun-php ^2.4
  - php-http/multipart-stream-builder 1.0.0

## Installation
You can install this module either via Composer or by placing it in app/code.

### Composer (preferred)
1. Require the package:
   - If available via your satis/packagist: `composer require select-co/module-mailgun`
   - If using this repository directly, add a VCS repository entry in your root composer.json and then `composer require select-co/module-mailgun`.
2. Enable and set up the module:
   - `bin/magento module:enable SelectCo_Mailgun`
   - `bin/magento setup:upgrade`
   - In production mode: `bin/magento setup:di:compile` and `bin/magento setup:static-content:deploy -f`

### Manual installation (app/code)
1. Copy this directory to `app/code/SelectCo/Mailgun`.
2. Run:
   - `bin/magento module:enable SelectCo_Mailgun`
   - `bin/magento setup:upgrade`
   - In production mode: `bin/magento setup:di:compile` and `bin/magento setup:static-content:deploy -f`

## Configuration
Admin Path: Stores > Configuration > SelectCo Settings > Mailgun Config

- General
  - Enabled: Turn Mailgun delivery on/off. When disabled, Magento uses its default transport.

- API
  - Sending Domain Name: Your Mailgun sending domain (e.g., mg.example.com)
  - API Key: Your Mailgun Private API key (stored encrypted; treated as sensitive)
  - Mailgun Endpoint: Choose between:
    - https://api.mailgun.net/ (US Region)
    - https://api.eu.mailgun.net/ (EU Region)

- Debug
  - Debug Enabled: Enable HTTP client debug output on requests. Note: Mailgun may accept messages in test/debug flows but not deliver them; refer to your Mailgun plan for billing implications.
  - Pastebin Enabled: Enable sending requests to a Mailgun Request Bin for inspection
  - Pastebin Id: The request bin ID to use when Pastebin is enabled

- Send Test Email
  - Provides a From and To field and a "Send Test" button. The request is sent to the controller `selectco_mailgun/system_config_mailgun/test` and the result is displayed inline.

## How it works
- The module declares a plugin on `Magento\Email\Model\Transport` (see etc/di.xml). When the module is enabled, `aroundSendMessage` intercepts `sendMessage()` and delegates delivery to `SelectCo\Mailgun\Model\Mailgun`.
- The Mailgun model builds parameters from Magento's email message object:
  - to/cc/bcc/from/reply-to, subject
  - html body (decoded from quoted-printable)
  - attachments (filename + raw content)
- Endpoint selection:
  - If Pastebin is enabled and a Pastebin Id is set, requests are sent to `http://bin.mailgun.net/{id}` for inspection
  - Otherwise, requests are sent to the chosen Mailgun API endpoint (US/EU)

## Email templates
- Test email template ID: `selectco_mailgun_test_email`
- File: `view/adminhtml/email/test_email.html`

## Troubleshooting
- Verify your Mailgun domain is verified and authorized to send
- Ensure DNS records (SPF/DKIM) are correctly configured for your sending domain
- Make sure the API key and endpoint match your Mailgun region
- Use Debug and/or Pastebin to inspect payloads if messages are not delivered
- Check Magento logs for exceptions and the Mailgun event logs for rejected messages

## License
MIT. See LICENSE.

## Support
If you have a feature request or spotted a bug or a technical problem, create a GitHub issue.