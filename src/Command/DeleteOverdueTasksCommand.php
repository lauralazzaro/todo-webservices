<?php

namespace App\Command;

use App\Repository\TaskRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'DeleteOverdueTasks',
    description: 'Delete all tasks overdue and not completed',
)]
class DeleteOverdueTasksCommand extends Command
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        parent::__construct();

        $this->taskRepository = $taskRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Marking overdue items as deleted');

        $now = New \DateTime();
        $now->setTime(0, 0);

        $overdueTasks = $this->taskRepository->findOverdueTasks();

        if (empty($overdueTasks)) {
            $io->success('No overdue items found.');
            return Command::SUCCESS;
        }

        foreach ($overdueTasks as $task) {
            $task->setDeletedAt($now);
            $this->taskRepository->save($task, true);
        }

        $totalOverDueTasks = count($overdueTasks);
        $io->success("[$totalOverDueTasks] overdue tasks have been deleted.");

        return Command::SUCCESS;
    }
}
