<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

    public function testLoginPageIsUp()
    {
        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser
        $client = static::createClient();

        $client->request('GET', '/login');
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneBy(['username' => 'user']);

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // should redirect to the tasks page

        // test e.g. the profile page
        $client->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();
    }
}
