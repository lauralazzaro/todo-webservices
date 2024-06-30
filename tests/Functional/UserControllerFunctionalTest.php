<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerFunctionalTest extends WebTestCase
{
    private KernelBrowser $client;
    private ?object $userRepository;
    private ?object $router;

    /**
     * @throws Exception
     */
    public function testEditAction(): void
    {
        $testUser = $this->userRepository->findOneByRole('ROLE_USER');
        $this->client->loginUser($testUser);

        $url = $this->router->generate('user_edit', ['id' => $testUser->getId()]);

        $crawler = $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful('Cannot find user edit page');

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[password][first]'] = '1234';
        $form['user_edit[password][second]'] = '1234';

        $this->client->submit($form);

        $this->client->followRedirect();

        $this->assertSelectorTextContains(
            'div.alert-success',
            'You successfully update your password',
            'The flash message did not appear'
        );
    }

    public function testEditUserPagesNeedsPermission()
    {
        $testUser = $this->userRepository->findOneByRole('ROLE_USER');
        $this->client->loginUser($testUser);

        $testAdmin = $this->userRepository->findOneByRole('ROLE_ADMIN');
        $url = $this->router->generate('user_edit', ['id' => $testAdmin->getId()]);

        $this->client->request('GET', $url);

        $this->assertResponseRedirects();
        $this->client->followRedirect();

        $this->assertSelectorTextContains(
            'div.alert-danger',
            'You don\'t have the rights to access this page.',
            'The flash message did not appear'
        );

        $url = $this->router->generate('user_edit_generated_password', ['id' => $testAdmin->getId()]);

        $this->client->request('GET', $url);

        $this->assertResponseRedirects();
        $this->client->followRedirect();

        $this->assertSelectorTextContains(
            'div.alert-danger',
            'You don\'t have the rights to access this page.',
            'The flash message did not appear'
        );
    }

    public function testEditGeneratedPassword(): void
    {
        $testUser = $this->userRepository->findOneByRole('ROLE_USER');
        $this->client->loginUser($testUser);

        $url = $this->router->generate('user_edit_generated_password', ['id' => $testUser->getId()]);

        $crawler = $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful('Cannot find user edit page');

        $form = $crawler->selectButton('Modifier')->form();
        $form['user_edit[password][first]'] = '12345';
        $form['user_edit[password][second]'] = '12345';

        $this->client->submit($form);

        $this->client->followRedirect();

        $this->assertSelectorTextContains(
            'div.alert-success',
            'You successfully create your password',
            'The flash message did not appear'
        );
    }

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->router = static::getContainer()->get('router');
    }
}
