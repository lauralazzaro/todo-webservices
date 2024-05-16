<?php

namespace App\Tests\Smoke;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResetPasswordControllerSmokeTest extends WebTestCase
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
}