<?php
namespace Helium\EmailNotifications;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;

class SwiftMailerEngine implements EmailNotificationInterface
{
    private Email $_email;
    private Mailer $_mailer;
    private string $_dsn;

    public function __construct()
    {
        $this->_email = new Email();
    }

    public function sendEmail()
    {
        if (!$this->_mailer) {
            throw new \Exception("Mailer not initialized. Call setServerSettings() first.");
        }
        return $this->_mailer->send($this->_email);
    }

    public function setServerSettings(array $serverSettings)
    {
        $this->_dsn = sprintf(
            'smtp://%s:%s@%s:%d',
            urlencode($serverSettings['mail_username']),
            urlencode($serverSettings['mail_password']),
            $serverSettings['mail_host'],
            $serverSettings['mail_port']
        );

        $transport = Transport::fromDsn($this->_dsn);
        $this->_mailer = new Mailer($transport);
    }

    public function setFromAddress(string $address, string $name = null)
    {
        $this->_email->from(new Address($address, $name ?? ''));
    }

    public function setRecipients(string $address, string $name = null)
    {
        $this->_email->to(new Address($address, $name ?? ''));
    }

    public function setCC(string $address, string $name = null)
    {
        $this->_email->cc(new Address($address, $name ?? ''));
    }

    public function setBCC(string $address, string $name = null)
    {
        $this->_email->bcc(new Address($address, $name ?? ''));
    }

    public function setAttachment($attachment, string $name = null)
    {
        $this->_email->attachFromPath($attachment, $name);
    }

    public function setSubject(string $subject)
    {
        $this->_email->subject($subject);
    }

    public function setBody(string $body)
    {
        $this->_email->html($body);
    }

    public function setAltBody(string $altBody)
    {
        $this->_email->text($altBody);
    }

    public function setCustomHeader(string $header, string $value)
    {
        $this->_email->getHeaders()->addTextHeader($header, $value);
    }
}
