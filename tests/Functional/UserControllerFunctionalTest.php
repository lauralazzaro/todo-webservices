<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerFunctionalTest extends WebTestCase
{
    const USER = 'user';

    /**
     * @throws Exception
     */
    public function testEditAction(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => self::USER]);
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/users/' . $testUser->getId() . '/edit');

        $this->assertResponseIsSuccessful('Cannot find user edit page');

        $form = $crawler->selectButton('Modifier')->form();

        $form['user_edit[password][first]'] = '123';
        $form['user_edit[password][second]'] = '123';

        $client->submit($form);

        $client->followRedirect();

        $this->assertSelectorTextContains(
            'div.alert-success',
            'You successfully update your password',
            'The flash message did not appear'
        );
    }
}
