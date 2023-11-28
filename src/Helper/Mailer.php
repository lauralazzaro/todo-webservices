<?php

namespace App\Helper;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Mailer
{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmail(string $recipient, string $subject, string $content): void
    {

        $email = (new Email())
            ->from($_ENV['MAILER_FROM'])
            ->to($_ENV['MAILER_TO'])
            ->subject($subject)
            ->html($content);

        $this->mailer->send($email);
    }
}