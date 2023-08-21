<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testCreateAction()
    {

        $client = static::createClient();

        // Simulate a user being authenticated
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneBy(['username' => 'user']);
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
     */
    public function testEditPageIsAccessible()
    {
        $client = static::createClient();
        $task = $this->createMock(Task::class);
        $task->method('getId')->willReturn(1);

        // retrieve the test admin
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username' => 'admin']);
        $client->loginUser($testUser);

        $client->request('GET', '/tasks/1/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testEditPageRequiresAuthorization()
    {
        $client = static::createClient();

        // Simulate a user being authenticated
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'user']);
        $client->loginUser($testUser);

        $client->request('GET', '/tasks/1/edit');

        // the owner is admin so user is not authorized
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testEditActionSuccess()
    {
        $client = static::createClient();

        // Simulate a user being authenticated
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'user']);
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
}
