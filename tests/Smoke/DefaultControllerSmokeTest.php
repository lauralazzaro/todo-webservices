<?php

namespace App\Tests\Smoke;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerSmokeTest extends WebTestCase
{
    public function testIndexRoute(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h3', 'Welcome to the ToDo App');
    }
}
