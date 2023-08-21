<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TaskControllerTest extends WebTestCase
{
    const USER = 'user';
    const ADMIN = 'admin';
    const ANONYMOUS = 'anonymous';

    /**
     * @throws Exception
     */
    public function testCreateAction()
    {

        $client = static::createClient();

        // retrieve the test user
        $testUser = $this->loadUserByUsername(self::USER);
        $client->loginUser($testUser);

        // Make a request to the login route
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
        ); // Assert the redirection

        $client->followRedirect(); // Follow the redirection

        $this->assertSelectorTextContains(
            'div.alert-success',
            'Task created successfully.',
            'The flash message did not appear'
        );
    }

    public function testRedirectIfNotLoggedIn()
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
    public function testEditPageIsAccessible()
    {
        $client = static::createClient();
        $task = $this->createMock(Task::class);
        $task->method('getId')->willReturn(1);

        $testUser = $this->loadUserByUsername(self::ADMIN);
        $client->loginUser($testUser);

        $task = $this->loadTaskFromDatabaseByOwner($testUser);

        $client->request('GET', '/tasks/' . $task->getId() .'/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     * @throws Exception
     *
     */
    public function testEditPageRequiresAuthorization()
    {
        $client = static::createClient();

        $testUser = $this->loadUserByUsername(self::USER);
        $client->loginUser($testUser);


        // load admin user and one of his task to ensure that a ROLE_USER cannot access it
        $adminUser = $this->loadUserByUsername(self::ADMIN);
        $task = $this->loadTaskFromDatabaseByOwner($adminUser);

        $client->request('GET', '/tasks/' . $task->getId() .'/edit');


        // the owner is admin so user is not authorized
        $this->assertEquals(
            403,
            $client->getResponse()->getStatusCode(),
            'Opened the page even without authorization'
        );
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws Exception
     */
    public function testEditActionSuccess()
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
    public function testToggleTaskAction()
    {
        $client = static::createClient();

        $testUser = $this->loadUserByUsername(self::USER);
        $client->loginUser($testUser);

        // Load a task from your database (you may need to set up fixtures or use a mock)
        $user = $this->loadUserByUsername(self::USER); // Implement this method

        $task = $this->loadTaskFromDatabaseByOwner($user);

        $crawler = $client->request('GET', '/tasks');

        // Find the form in the response by its action attribute
        $form = $crawler->filter('form[action="/tasks/' . $task->getId() . '/toggle"]')->form();

        // Submit the form
        $client->submit($form);

        // Check if the response is a redirect or any other expected response
        $this->assertTrue($client->getResponse()->isRedirect());

        // Follow the redirect
        $client->followRedirect();

        // Reload the task from the database to check if it has been toggled
        $updatedTask = $this->loadTaskFromDatabaseByOwner($user); // Implement this method
        $this->assertEquals(!$task->isDone(), $updatedTask->isDone());
    }

    /**
     * @throws Exception
     */
    public function testDeletePageRequiresAuthorization()
    {
        $client = static::createClient();

        $testUser = $this->loadUserByUsername(self::USER);
        $client->loginUser($testUser);


        // load admin user and one of his task to ensure that a ROLE_USER cannot access it
        $adminUser = $this->loadUserByUsername(self::ADMIN);
        $task = $this->loadTaskFromDatabaseByOwner($adminUser);

        $client->request('GET', '/tasks/' . $task->getId() .'/delete');

        // the owner is admin so user is not authorized
        $this->assertEquals(
            403,
            $client->getResponse()->getStatusCode(),
            'Opened the page even without authorization'
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

    /**
     * @throws Exception
     */
    private function loadUserByUsername($username)
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        return $userRepository->findOneBy(['username' => $username]);
    }
}
