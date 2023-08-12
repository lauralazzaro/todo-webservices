<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginRedirectWhenUserAuthenticated()
    {
        $client = static::createClient();

        // Simulate a user being authenticated
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneBy(['username' => 'user']);
        $client->loginUser($testUser);

        // Make a request to the login route
        $client->request('GET', '/login');

        // Assert that the response is a redirect to 'task_list' route
        $this->assertResponseRedirects(
            $client->getResponse()->isRedirect('task_list'),
            302,
            'Redirection to task_list'
        );

        // Follow the redirect and assert response
        $client->followRedirect();
        $this->assertResponseIsSuccessful('Could not request task_list after login');
    }

    public function testLoginDisplayLoginPageWhenUserNotAuthenticated()
    {
        $client = static::createClient();

        // Make a request to the login route
        $crawler = $client->request('GET', '/login');

        // Assert that the response is successful
        $this->assertResponseIsSuccessful(
            'Could not open login page'
        );

        // Assert that the login form is present
        $this->assertCount(
            1,
            $crawler->filter('form[id="loginForm"]'),
            'Logig form not found'
        );
    }

    public function testLogoutThrowsLogicException()
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $this->assertNull(
            $client->getContainer()->get('security.token_storage')->getToken(),
            'Logout failed'
        );
    }
}
