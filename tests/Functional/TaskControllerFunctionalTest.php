<?php

namespace App\Tests\Functional;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerFunctionalTest extends WebTestCase
{
    private const USER = 'user';
    private const ADMIN = 'admin';
    private const ANONYMOUS = 'anonymous';

    /**
     * @throws Exception
     */
    public function testCreateAction(): void
    {

        $client = static::createClient();

        $testUser = $this->loadUserByUsername(self::USER);
        $client->loginUser($testUser);

        $client->request('GET', '/login');

        $crawler = $client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful('Cannot open create task page');

        $form = $crawler->selectButton('Add')->form();
        $form['task[title]'] = 'New Task Title';
        $form['task[content]'] = 'New content for this task';

        $client->submit($form);

        $this->assertResponseRedirects(
            $client->getResponse()->isRedirect('task_list'),
            302,
            'Did not redirect to task_list page'
        );

        $client->followRedirect();

        $this->assertSelectorTextContains(
            'div.alert-success',
            'Task created successfully.',
            'The flash message did not appear'
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

    public function testRedirectIfNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');

        $this->assertResponseRedirects(
            $client->getResponse()->isRedirect('task_list'),
            302,
            'Did not redirect to login page from /tasks'
        );

        $client->request('GET', '/tasks/create');

        $this->assertResponseRedirects(
            $client->getResponse()->isRedirect('task_list'),
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
        $client = static::createClient();
        $task = $this->createMock(Task::class);
        $task->method('getId')->willReturn(1);

        $testUser = $this->loadUserByUsername(self::ADMIN);
        $client->loginUser($testUser);

        $task = $this->loadTaskFromDatabaseByOwner($testUser);

        $client->request('GET', '/tasks/' . $task->getId() . '/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     * @throws Exception
     *
     */
    public function testEditPageRequiresAuthorization(): void
    {
        $client = static::createClient();

        $testUser = $this->loadUserByUsername(self::USER);
        $client->loginUser($testUser);

        $adminUser = $this->loadUserByUsername(self::ADMIN);
        $task = $this->loadTaskFromDatabaseByOwner($adminUser);

        $client->request('GET', '/tasks/' . $task->getId() . '/edit');

        $this->assertSelectorTextContains(
            'div.alert-danger',
            'You cannot edit this task.',
            'The user deleted a task even if not owner or admin'
        );
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws Exception
     */
    public function testEditActionSuccess(): void
    {
        $client = static::createClient();

        $testUser = $this->loadUserByUsername(self::USER);
        $client->loginUser($testUser);

        $task = $this->createMock(Task::class);
        $task->method('getId')->willReturn(2);

        $client->request('GET', '/tasks/2/edit');

        $crawler = $client->request('GET', '/tasks/2/edit');

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            'Could not open task page'
        );

        $form = $crawler->selectButton('Update')->form();

        $form['task[title]'] = 'New Task Title edit';
        $form['task[content]'] = 'New content for this task';

        $client->submit($form);

        $this->assertEquals(
            302,
            $client->getResponse()->getStatusCode(),
            'Did not redirect to task list'
        );
    }

    /**
     * @throws Exception
     */
    public function testToggleTaskAction(): void
    {
        $client = static::createClient();

        $testUser = $this->loadUserByUsername(self::USER);
        $client->loginUser($testUser);

        $user = $this->loadUserByUsername(self::USER);

        $task = $this->loadTaskFromDatabaseByOwner($user);

        $crawler = $client->request('GET', '/tasks');

        $link = $crawler->filter('a[href="/tasks/' . $task->getId() . '/toggle"]')->each(function ($node) {
            if (trim($node->text()) === "Click to mark as done") {
                return $node;
            }
        });

        $client->click($link[0]->link());

        $this->assertTrue($client->getResponse()->isRedirect());

        $client->followRedirect();

        $updatedTask = $this->loadTaskFromDatabaseByOwner($user);
        $this->assertEquals(!$task->isDone(), $updatedTask->isDone());
    }

    /**
     * @throws Exception
     */
    public function testDeletePageRequiresAuthorization(): void
    {
        $client = static::createClient();

        $testUser = $this->loadUserByUsername(self::USER);
        $client->loginUser($testUser);

        $adminUser = $this->loadUserByUsername(self::ADMIN);
        $task = $this->loadTaskFromDatabaseByOwner($adminUser);

        $client->request('GET', '/tasks/' . $task->getId() . '/delete');

        $this->assertEquals(
            302,
            $client->getResponse()->getStatusCode(),
            'Did not redirect when trying to open a delete task page'
        );
    }

    /**
     * @throws Exception
     */
    public function testDeleteSuccess(): void
    {
        $client = static::createClient();

        $testUser = $this->loadUserByUsername(self::USER);
        $client->loginUser($testUser);

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = new Task();
        $task->setTitle('title');
        $task->setContent('content');
        $task->setUser($testUser);

        $taskRepository->save($task, true);

        $client->request('GET', '/tasks/' . $task->getId() . '/delete');

        $this->assertEquals(
            302,
            $client->getResponse()->getStatusCode(),
            'Did not redirect when trying to open a delete task page'
        );

        $client->followRedirect();

        $this->assertSelectorTextContains(
            'div.alert-warning',
            'The task has been successfully deleted.',
            'The flash message did not appear'
        );
    }

    /**
     * @throws Exception
     */
    private function loadTaskFromDatabaseByOwner($user)
    {
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        return $taskRepository->findOneBy(['user' => $user]);
    }
}
