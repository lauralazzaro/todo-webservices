<?php

namespace App\Tests\Functional;

use App\Helper\Constants;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerFunctionalTest extends WebTestCase
{
    private const USER = 'user';
    private const ADMIN = 'admin';
    private KernelBrowser $client;
    private ?object $userRepository;
    private ?object $taskRepository;
    private ?object $router;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
        $this->router = static::getContainer()->get('router');
    }

    /**
     * @throws Exception
     */
    public function testCreateAction(): void
    {
        $testUser = $this->userRepository->findOneBy(['username' => self::USER]);
        $this->client->loginUser($testUser);

        $this->client->request('GET', '/login');

        $crawler = $this->client->request('GET', Constants::TASK_CREATE_URL);
        $this->assertResponseIsSuccessful('Cannot open create task page');

        $form = $crawler->selectButton('Add')->form();
        $form['task[title]'] = 'New Task Title';
        $form['task[content]'] = 'New content for this task';

        $this->client->submit($form);

        $this->assertResponseRedirects(
            $this->client->getResponse()->isRedirect(Constants::TASK_LIST_NAME),
            302,
            'Did not redirect to task_list page'
        );

        $this->client->followRedirect();

        $this->assertSelectorTextContains(
            'div.alert-success',
            'Task created successfully.',
            'The flash message did not appear'
        );
    }

    public function testRedirectIfNotLoggedIn(): void
    {
        $this->client->request('GET', Constants::TASK_LIST_URL);

        $this->assertResponseRedirects(
            $this->client->getResponse()->isRedirect(Constants::TASK_LIST_NAME),
            302,
            'Did not redirect to login page from /tasks'
        );

        $this->client->request('GET', Constants::TASK_CREATE_URL);

        $this->assertResponseRedirects(
            $this->client->getResponse()->isRedirect(Constants::TASK_LIST_NAME),
            302,
            'Did not redirect to login page from /tasks/create'
        );
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws Exception
     */
    public function testEditPageIsAccessible(): void
    {
        $testUser = $this->userRepository->findOneBy(['username' => self::ADMIN]);
        $this->client->loginUser($testUser);

        $task = $this->taskRepository->findOneBy(['user' => $testUser]);

        $this->client->request('GET', '/tasks/' . $task->getId() . '/edit');

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            'Owner did not open task page'
        );
    }

    /**
     * @return void
     * @throws Exception
     *
     */
    public function testEditPageRequiresAuthorization(): void
    {
        $testUser = $this->userRepository->findOneBy(['roles' => ['["ROLE_USER"]']]);
        $this->client->loginUser($testUser);

        $adminUser = $this->userRepository->findOneBy(['username' => self::ADMIN]);
        $task = $this->taskRepository->findOneBy(['user' => $adminUser]);

        $this->client->request('GET', '/tasks/' . $task->getId() . '/edit');
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    /**
     * @throws Exception
     */
    public function testEditActionSuccess(): void
    {
        $testUser = $this->userRepository->findOneBy(['username' => self::ADMIN]);
        $this->client->loginUser($testUser);

        $taskToEdit = $this->taskRepository->findOneBy(['user' => $testUser]);
        $url = $this->router->generate(Constants::TASK_EDIT_NAME, ['id' => $taskToEdit->getId()]);

        $crawler = $this->client->request('GET', $url);

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            'Could not open task page'
        );

        $form = $crawler->selectButton('Update')->form();
        $form['task[title]'] = 'New Task Title edit';
        $form['task[content]'] = 'New content for this task';

        $this->client->submit($form);

        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode(),
            'Did not redirect to task list'
        );
    }

    /**
     * @throws Exception
     */
    public function testToggleTaskAction(): void
    {
        $testUser = $this->userRepository->findOneBy(['username' => self::USER]);
        $this->client->loginUser($testUser);

        $task = $this->taskRepository->findOneBy(['user' => $testUser]);

        $before = $task->isDone();
        $this->client->request('GET', '/tasks/' . $task->getId() . '/toggle');

        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertNotEquals($before, $task->isDone());
    }

    /**
     * @throws Exception
     */
    public function testDeletePageRequiresAuthorization(): void
    {
        $testUser = $this->userRepository->findOneBy(['username' => self::USER]);
        $this->client->loginUser($testUser);

        $adminUser = $this->userRepository->findOneBy(['username' => self::ADMIN]);
        $task = $this->taskRepository->findOneBy(['user' => $adminUser]);

        $this->client->request('GET', '/tasks/' . $task->getId() . '/delete');

        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode(),
            'Did not redirect when trying to open a delete task page'
        );
    }

    /**
     * @throws Exception
     */
    public function testDeleteSuccess(): void
    {
        $testUser = $this->userRepository->findOneBy(['username' => self::USER]);
        $this->client->loginUser($testUser);

        $task = $this->taskRepository->findOneBy(['user' => $testUser]);
        $this->client->request('GET', '/tasks/' . $task->getId() . '/delete');

        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode(),
            'Did not redirect when trying to open a delete task page'
        );

        $this->client->followRedirect();

        $this->assertSelectorTextContains(
            'div.alert-warning',
            'The task has been successfully deleted.',
            'The flash message did not appear'
        );
    }
}
