<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Helper\Mailer;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use App\Helper\Constants;

class AdminControllerFunctionalTest extends WebTestCase
{
    private const ADMIN = 'admin';
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
    public function testDenyAccessIfNotAdmin(): void
    {
        $testUser = $this->userRepository->findOneBy(['roles' => ['["ROLE_USER"]']]);
        $this->client->loginUser($testUser);

        $this->client->request('GET', Constants::ADMIN_USER_LIST_URL);

        $this->assertEquals(
            403,
            $this->client->getResponse()->getStatusCode(),
            'Opened the users page even without authorization'
        );
    }

    /**
     * @throws Exception
     */
    public function testAllowAccessToUsersPage(): void
    {
        $testUser = $this->userRepository->findOneBy(['username' => self::ADMIN]);
        $this->client->loginUser($testUser);

        $this->client->request('GET', Constants::ADMIN_USER_LIST_URL);

        $this->assertResponseIsSuccessful('Cannot view create users page');
    }

    /**
     * @throws Exception
     */
    public function testForbiddenCreateUserIfNotAdmin(): void
    {
        $testUser = $this->userRepository->findOneBy(['roles' => ['["ROLE_USER"]']]);
        $this->client->loginUser($testUser);

        $this->client->request('GET', Constants::ADMIN_USER_CREATE_URL);

        $this->assertEquals(
            403,
            $this->client->getResponse()->getStatusCode(),
            'Opened create user page even if ROLE_USER'
        );
    }

    /**
     * @throws Exception
     */
    public function testCreateUserSuccess(): void
    {
        $testUser = $this->userRepository->findOneBy(['username' => self::ADMIN]);
        $this->client->loginUser($testUser);

        $this->client->request('GET', Constants::ADMIN_USER_CREATE_URL);

        $this->assertResponseIsSuccessful('Error viewing create user page even if ROLE_ADMIN');

        $testUserToCreate = new User();
        $testUserToCreate->setEmail('test_user_create_success@email.com');
        $testUserToCreate->setUsername('test_user_create_success');
        $testUserToCreate->setPassword('abc123!');
        $testUserToCreate->setIsPasswordGenerated(false);
        $testUserToCreate->setRoles(['ROLE_USER']);

        $this->userRepository->save($testUserToCreate, true);

        $lastUser = $this->userRepository->findOneBy(['email' => $testUserToCreate->getEmail()]);

        $this->assertNotNull($lastUser, 'User successfully created');
    }

    /**
     * @throws Exception
     */
    public function testEditUserSuccess(): void
    {
        $adminUser = $this->userRepository->findOneBy(['username' => self::ADMIN]);
        $this->client->loginUser($adminUser);

        $testUserToEdit = $this->userRepository->findOneBy(['roles' => ['["ROLE_USER"]']]);

        $url = $this->router->generate(Constants::ADMIN_USER_EDIT_NAME, ['id' => $testUserToEdit->getId()]);

        $crawler = $this->client->request('GET', $url);

        $form = $crawler->filter('#admin_edit_user_form')->form();
        $form['admin_edit_user[roles]'] = ['ROLE_ADMIN'];

        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertSelectorTextContains(
            'div.alert-success',
            'User successfully modified',
            'The flash message did not appear'
        );

        // Verify the user's role has been updated
        $updatedUser = $this->userRepository->findOneBy(['id' => $testUserToEdit->getId()]);
        $this->assertContains('ROLE_ADMIN', $updatedUser->getRoles());
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function testSendEmail(): void
    {
        $mailerMock = $this->createMock(MailerInterface::class);

        $twigMock = $this->createMock(Environment::class);

        $mailer = new Mailer($mailerMock, $twigMock);

        $subject = 'Test Subject';
        $temporaryPassword = 'TestPassword123';
        $mailerTo = $_ENV['MAILER_TO'];

        $twigMock->expects($this->once())
            ->method('render')
            ->willReturn('<html>Mocked email content</html>');

        $mailerMock->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) use ($subject, $temporaryPassword, $mailerTo) {
                $expectedContent = '<html>Mocked email content</html>';
                $this->assertEquals($expectedContent, $email->getHtmlBody());

                return true;
            }));

        $mailer->sendEmail($subject, $temporaryPassword, $mailerTo);
    }
}
