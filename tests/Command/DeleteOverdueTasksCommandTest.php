<?php

namespace App\Tests\Command;

use App\Entity\Task;
use App\Repository\TaskRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DeleteOverdueTasksCommandTest extends KernelTestCase
{
    private ?object $taskRepository;

    public function testExecute(): void
    {
        $overdueTask1 = new Task();
        $overdueTask1->setTitle('Overdue Task 1');
        $overdueTask1->setContent('Content overdue Task 1');
        $overdueTask1->setDeadline(new DateTime('-1 day'));

        $overdueTask2 = new Task();
        $overdueTask2->setTitle('Overdue Task 2');
        $overdueTask2->setContent('Content overdue Task 2');
        $overdueTask2->setDeadline(new DateTime('-2 days'));

        $this->taskRepository->save($overdueTask1, true);
        $this->taskRepository->save($overdueTask2, true);

        $application = new Application(self::$kernel);
        $command = $application->find('DeleteOverdueTasks');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[2] overdue tasks have been deleted.', $output);

        $commandTester->execute([]);
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('No overdue items found.', $output);
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
    }
}
