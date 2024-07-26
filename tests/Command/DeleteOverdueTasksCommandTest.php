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
        $application = new Application(self::$kernel);
        $command = $application->find('DeleteOverdueTasks');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('overdue tasks have been deleted.', $output);

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
