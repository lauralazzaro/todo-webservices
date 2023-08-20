<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
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
}
