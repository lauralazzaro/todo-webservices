<?php

namespace App\Tests\Controller;

use App\Controller\AdminController;
use App\Entity\User;
use App\Helper\Mailer;
use App\Helper\UserHelper;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AdminControllerTest extends WebTestCase
{
    const USER = 'user';
    const ADMIN = 'admin';
    const ANONYMOUS = 'anonymous';

    private $userRepository;
    private $userHelper;
    private $mailer;

    protected function setUp(): void
    {
        self::bootKernel();

        // Mocking services
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->userHelper = $this->createMock(UserHelper::class);
        $this->mailer = $this->createMock(Mailer::class);
    }

    public function testCreateAction()
    {
        // Mocking form
        $form = $this->createMock(FormInterface::class);
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);

        $request = new Request();
        $controller = new AdminController();
        $controller->setContainer(static::$container);

        // Setting up the controller with mocked dependencies
        $controller->setUserRepository($this->userRepository);
        $controller->setUserHelper($this->userHelper);
        $controller->setMailer($this->mailer);

        // Mocking the form creation
        $controller->method('createForm')->willReturn($form);

        // Executing the action
        $response = $controller->createAction($request, $this->userRepository, $this->userHelper, $this->mailer);

        // Assertions
        $this->assertInstanceOf(RedirectResponse::class, $response);

        // Check that the email was not sent
        $mailCollector = self::$container->get('profiler')->getCollector('mailer');
        $this->assertSame(0, $mailCollector->getMessageCount(), 'No emails should have been sent');
    }
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

//    /**
//     * @throws Exception
//     */
//    public function testCreateUserSuccess()
//    {
//        $client = static::createClient();
//
//        $testUser = $this->loadUserByUsername(self::ADMIN);
//        $client->loginUser($testUser);
//
//        $crawler = $client->request('GET', '/admin/users/create');
//
//        $this->assertResponseIsSuccessful('Error viewing create user page even if ROLE_ADMIN');
//
//        $form = $crawler->selectButton('Create user')->form();
//
//        $form['admin_create_user[email]'] = 'your_email@example.com';
//        $form['admin_create_user[roles]'] = ['ROLE_ADMIN'];
//        $form['admin_create_user[username]'] = 'your_username';
//
//        $client->submit($form);
//
//        $client->followRedirect();
//
//        $this->assertResponseIsSuccessful('Did not create user');
//
//        $this->assertSelectorTextContains(
//            'div.alert-success',
//            'New user created',
//            'The flash message did not appear'
//        );
//
//        // remove user
//        $userRepository = static::getContainer()->get(UserRepository::class);
//        $lastUser = $userRepository->findOneBy(['email' => 'your_email@example.com']);
//        $userRepository->remove($lastUser, true);
//    }

    public function testEditUserSuccess()
    {
        $client = static::createClient();

        // Simulate an authenticated user with ROLE_ADMIN
        $adminUser = $this->loadUserByUsername(self::ADMIN);
        $client->loginUser($adminUser);

        //create new user
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUserToEdit=new User();
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

        // Optionally, you can also check that the user entity in the database has been updated
        $updatedUser = $userRepository->find($testUserToEdit->getId());
        $this->assertContains('ROLE_ADMIN', $updatedUser->getRoles());

        $userRepository->remove($updatedUser, true);
    }
}
