<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Exception;

class UserControllerTest extends WebTestCase
{
    const USER = 'user';

    /**
     * @throws Exception
     */
    public function testEditAction()
    {
        $client = static::createClient();

        // retrieve the test user
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => self::USER]);
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/users/' . $testUser->getId() . '/edit');

        $this->assertResponseIsSuccessful('Cannot find user edit page');

        // Select the form and create a form object
        $form = $crawler->selectButton('Modifier')->form();

        $form['user_edit[password][first]'] = '123';
        $form['user_edit[password][second]'] = '123';

        // Submit the form
        $client->submit($form);

        // Follow the redirect
        $client->followRedirect();

        // Check for a success flash message or other indicators of success on the redirected page
        $this->assertSelectorTextContains(
            'div.alert-success',
            'You successfully update your password',
            'The flash message did not appear'
        );
    }
}
