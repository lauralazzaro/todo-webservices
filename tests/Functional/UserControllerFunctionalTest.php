<?php

namespace App\Tests\Functional;

use App\Helper\Constants;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerFunctionalTest extends WebTestCase
{
    const USER = 'user';
    private KernelBrowser $client;
    private ?object $userRepository;
    private ?object $router;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->router = static::getContainer()->get('router');
    }

    /**
     * @throws Exception
     */
    public function testEditAction(): void
    {
        $testUser = $this->userRepository->findOneBy(['username' => self::USER]);
        $this->client->loginUser($testUser);

        $url = $this->router->generate(Constants::USER_EDIT_NAME, ['id' => $testUser->getId()]);

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
}
