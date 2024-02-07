<?php

namespace App\Helper;

use Twig\Environment;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Mailer
{

    private $mailer;
    private $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
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

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function createUserEmailBody($temporaryPassword): string
    {
        $template = 'email/create_user.html.twig';

        return $this->twig->render($template, [
            'temporaryPassword' => $temporaryPassword,
        ]);
    }
}