<?php

namespace App\Tests\Functional;

use App\Helper\Constants;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerFunctionalTest extends WebTestCase
{
    public function testLoginRedirectWhenUserAuthenticated(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByRole(Constants::ROLE_USER);
        $client->loginUser($testUser);

        $client->request('GET', '/login');

        $this->assertResponseRedirects(
            $client->getResponse()->isRedirect(Constants::TASK_LIST_NAME),
            302,
            'Redirection to task_list'
        );

        // Follow the redirect and assert response
        $client->followRedirect();
        $this->assertResponseIsSuccessful('Could not request task_list after login');
    }

    public function testLogout(): void
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $this->assertNull(
            $client->getContainer()->get('security.token_storage')->getToken(),
            'Logout failed'
        );
    }
}
