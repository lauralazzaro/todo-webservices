<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Response;

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

        $this->assertResponseStatusCodeSame(302, 'This should be a redirect.');

        $client->followRedirect();

        $request = $client->getRequest();

        $route = $request->attributes->get('_route');

        $this->assertSame(
            'app_check_email',
            $route,
            'The response should redirect to app_check_email.'
        );
    }

    public function testCheckTokenRemovedFromRequestUrl(): void
    {
        $client = static::createClient();

        $client->request('GET', '/reset-password/reset/valid_token');

        $crawler = $client->followRedirect();

        $targetUrl = $crawler->getUri();

        $this->assertStringNotContainsString('valid_token', $targetUrl);
    }
}
