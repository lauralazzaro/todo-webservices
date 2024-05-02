<?php

namespace App\Helper;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Environment;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Mailer
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendEmail(string $subject, string $temporaryPassword, string $mailerTo): void
    {
        $content = $this->createUserEmailBody($temporaryPassword);
        $email = (new Email())
            ->from($_ENV['MAILER_FROM'])
            ->to($mailerTo)
            ->subject($subject)
            ->html($content);

        $this->mailer->send($email);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function createUserEmailBody($temporaryPassword): string
    {
        $template = 'email/create_user.html.twig';

        return $this->twig->render($template, [
            'temporaryPassword' => $temporaryPassword,
        ]);
    }
}
