<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    const USER = 'user';
    const ADMIN = 'admin';
    const ANONYMOUS = 'anonymous';

    /**
     * @throws Exception
     */
    public function testDenyAccessIfNotAdmin()
    {
        $client = static::createClient();

        // retrieve the test user
        $testUser = $this->loadUserByUsername(self::USER);
        $client->loginUser($testUser);

        $client->request('GET', '/admin/users');

        $this->assertEquals(
            403,
            $client->getResponse()->getStatusCode(),
            'Opened the users page even without authorization'
        );

        $userForEdit = $this->loadUserByUsername(self::USER);
        $client->request('GET', '/admin/users/' . $userForEdit->getId() . '/edit');

        $this->assertEquals(
            403,
            $client->getResponse()->getStatusCode(),
            'Opened the edit user page without authorization'
        );
    }

    /**
     * @throws Exception
     */
    private function loadUserByUsername($username)
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        return $userRepository->findOneBy(['username' => $username]);
    }

    /**
     * @throws Exception
     */
    public function testAllowAccessToUsersPage()
    {
        $client = static::createClient();

        $testUser = $this->loadUserByUsername(self::ADMIN);
        $client->loginUser($testUser);

        $client->request('GET', '/admin/users');

        $this->assertResponseIsSuccessful('Cannot view create users page');
    }

    /**
     * @throws Exception
     */
    public function testForbiddenCreateUserIfNotAdmin()
    {
        $client = static::createClient();

        $testUser = $this->loadUserByUsername(self::USER);
        $client->loginUser($testUser);

        $client->request('GET', '/admin/users/create');

        $this->assertEquals(
            403,
            $client->getResponse()->getStatusCode(),
            'Opened create user page even if ROLE_USER'
        );
    }

    /**
     * @throws Exception
     */
    public function testCreateUserSuccess()
    {
        $client = static::createClient();

        $testUser = $this->loadUserByUsername(self::ADMIN);
        $client->loginUser($testUser);

        $client->request('GET', '/admin/users/create');

        $this->assertResponseIsSuccessful('Error viewing create user page even if ROLE_ADMIN');

        //create new user
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUserToCreate = new User();
        $testUserToCreate->setEmail('test_user@email.com');
        $testUserToCreate->setUsername('test_username');
        $testUserToCreate->setPassword('abc123!');
        $testUserToCreate->setIsPasswordGenerated(true);
        $testUserToCreate->setRoles(['ROLE_USER']);

        $userRepository->save($testUserToCreate, true);

        $lastUser = $userRepository->findOneBy(['email' => $testUserToCreate->getEmail()]);

        $this->assertNotNull($lastUser, 'User successfully created');

        // remove user
        $userRepository = static::getContainer()->get(UserRepository::class);
        $lastUser = $userRepository->findOneBy(['email' => $lastUser->getEmail()]);
        $userRepository->remove($lastUser, true);
    }

    public function testEditUserSuccess()
    {
        $client = static::createClient();

        // Simulate an authenticated user with ROLE_ADMIN
        $adminUser = $this->loadUserByUsername(self::ADMIN);
        $client->loginUser($adminUser);

        //create new user
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUserToEdit = new User();
        $testUserToEdit->setEmail('test_user@email.com');
        $testUserToEdit->setUsername('test_username');
        $testUserToEdit->setPassword('abc123!');
        $testUserToEdit->setIsPasswordGenerated(true);
        $testUserToEdit->setRoles(['ROLE_USER']);

        $userRepository->save($testUserToEdit, true);

        // Make a GET request to the edit page
        $crawler = $client->request('GET', '/admin/users/' . $testUserToEdit->getId() . '/edit');

        // Check that the response is successful
        $this->assertResponseIsSuccessful('Did not not find user to edit');

        // Select the form and create a form object
        $form = $crawler->selectButton('Modifier')->form();

        // Fill in the form fields with updated data
        // can only update roles
        $form['admin_edit_user[roles]'] = ['ROLE_ADMIN'];

        // Submit the form
        $client->submit($form);

        // Follow the redirect
        $client->followRedirect();

        // Check for a success flash message or other indicators of success on the redirected page
        $this->assertSelectorTextContains(
            'div.alert-success',
            'User successfully modified',
            'The flash message did not appear'
        );

        $updatedUser = $userRepository->find($testUserToEdit->getId());
        $this->assertContains('ROLE_ADMIN', $updatedUser->getRoles());

        $userRepository->remove($updatedUser, true);
    }
}
