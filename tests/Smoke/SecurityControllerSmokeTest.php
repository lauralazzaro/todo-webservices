<?php

namespace App\Tests\Smoke;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerSmokeTest extends WebTestCase
{
    public function testLoginPageFound(): void
    {
        $client = static::createClient();

        // Make a request to the login route
        $client->request('GET', '/login');

        // Assert that the response is successful
        $this->assertResponseIsSuccessful(
            'Could not open login page'
        );
    }
}
