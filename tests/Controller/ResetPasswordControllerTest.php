<?php

namespace App\Tests\Controller;

use App\Controller\ResetPasswordController;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class ResetPasswordControllerTest extends WebTestCase
{
    public function testRequestPasswordResetPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/reset-password');
        $this->assertResponseIsSuccessful();
    }

    public function testCheckEmailPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/reset-password/check-email');
        $this->assertResponseIsSuccessful();
    }

    public function testResetPasswordPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/reset-password/reset/token');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @throws Exception
     */
    public function testProcessSendingPasswordResetEmail(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/reset-password');

        $form = $crawler->filter('form[name=reset_password_request_form]')->form();
        $form['reset_password_request_form[email]'] = 'test@example.com';
        $client->submit($form);

        $response = $client->getResponse();
        $this->assertInstanceOf(RedirectResponse::class, $response, 'The response should be a RedirectResponse.');

        $this->assertSame(
            '/reset-password/check-email',
            $response->getTargetUrl(),
            'The response should redirect to app_check_email.'
        );
    }
}
