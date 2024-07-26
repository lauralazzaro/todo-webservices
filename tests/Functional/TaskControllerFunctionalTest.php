<?php

namespace App\Tests\Functional;

use App\Enum\TaskStatus;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerFunctionalTest extends WebTestCase
{
    private KernelBrowser $client;
    private ?object $userRepository;
    private ?object $taskRepository;
    private ?object $router;

    /**
     * @throws Exception
     */
    public function testCreateAction(): void
    {
        $testUser = $this->userRepository->findOneByRole('ROLE_USER');
        $this->client->loginUser($testUser);

        $this->client->request('GET', '/login');

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful('Cannot open create task page');

        $form = $crawler->selectButton('Add')->form();
        $form['task[title]'] = 'New Task Title';
        $form['task[content]'] = 'New content for this task';

        $this->client->submit($form);

        $this->assertResponseRedirects(
            $this->client->getResponse()->isRedirect('task_list'),
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
        $this->client->request('GET', '/tasks/create');

        $this->assertResponseRedirects(
            $this->client->getResponse()->isRedirect('task_list'),
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
        $testUser = $this->userRepository->findOneByRole('ROLE_ADMIN');
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
        $testUser = $this->userRepository->findOneByRole('ROLE_USER');
        $this->client->loginUser($testUser);

        $adminUser = $this->userRepository->findOneByRole('ROLE_ADMIN');
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
        $testUser = $this->userRepository->findOneByRole('ROLE_USER');
        $this->client->loginUser($testUser);

        $taskToEdit = $this->taskRepository->findOneBy(['user' => $testUser]);
        $url = $this->router->generate('task_edit', ['id' => $taskToEdit->getId()]);

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
    public function testChangeStatusTaskAction(): void
    {
        $testUser = $this->userRepository->findOneByRole('ROLE_USER');
        $this->client->loginUser($testUser);

        $task = $this->taskRepository->findOneBy(['status' => TaskStatus::TODO]);

        $currentStatus = $task->getStatus();

        $this->client->request('GET', '/tasks/' . $task->getId() . '/change-status/' . TaskStatus::DONE->value);

        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertNotEquals($currentStatus, $task->getStatus());
        $this->assertNotNull($task->getCreatedAt());
    }

    /**
     * @throws Exception
     */
    public function testDeletePageRequiresAuthorization(): void
    {
        $testUser = $this->userRepository->findOneByRole('ROLE_USER');
        $this->client->loginUser($testUser);

        $adminUser = $this->userRepository->findOneByRole('ROLE_ADMIN');
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
        $testUser = $this->userRepository->findOneByRole('ROLE_USER');
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
        $this->assertNotNull($task->getDeletedAt());
    }

    public function testAdminCanEditTaskWithoutUser()
    {
        $roleUser = $this->userRepository->findOneByRole('ROLE_USER');
        $roleAdmin = $this->userRepository->findOneByRole('ROLE_ADMIN');

        $taskAnonymous = $this->taskRepository->findOneBy(['user' => null]);

        $this->client->loginUser($roleUser);
        $this->client->request('GET', '/tasks/' . $taskAnonymous->getId() . '/edit');

        $this->assertResponseRedirects();

        $this->client->loginUser($roleAdmin);
        $this->client->request('GET', '/tasks/' . $taskAnonymous->getId() . '/edit');
        $this->assertResponseIsSuccessful();
    }

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
        $this->router = static::getContainer()->get('router');
    }
}
