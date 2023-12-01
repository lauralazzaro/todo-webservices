<?php

namespace App\Helper;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Mailer extends AbstractController
{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmail(string $subject, string $temporaryPassword, string $mailerTo): void
    {
        $content = $this->createUserEmailBody($temporaryPassword);

        $email = (new Email())
            ->from($_ENV['MAILER_FROM'])
            ->to($_ENV['MAILER_TO'])
            ->subject($subject)
            ->html($content);

        $this->mailer->send($email);
    }

    private function createUserEmailBody($temporaryPassword): string
    {
        $template = 'email/create_user.html.twig';

        return $this->renderView($template, [
            'temporaryPassword' => $temporaryPassword,
        ]);
    }
}