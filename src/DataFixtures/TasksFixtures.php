<?php

namespace App\DataFixtures;

use App\Entity\Task;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TasksFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    // @codeCoverageIgnoreStart
    public static function getGroups(): array
    {
        return ['dev', 'test'];
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(
        ObjectManager $manager
    ): void {
        for ($i = 1; $i <= 3; $i++) {
            $task = new Task();
            $task->setTitle('Task from user n. ' . $i);
            $task->setContent('This task was created by a user');
            $task->setUser($this->getReference('user'));
            $manager->persist($task);
            $manager->flush();
        }

        for ($i = 1; $i <= 3; $i++) {
            $task = new Task();
            $task->setTitle('Task from admin n. ' . $i);
            $task->setContent('This task was created by an admin');
            $task->setUser($this->getReference('admin'));
            $manager->persist($task);
            $manager->flush();
        }

        for ($i = 1; $i <= 5; $i++) {
            $task = new Task();
            $task->setTitle('Task without user n. ' . $i);
            $task->setContent('This task has no user');
            $manager->persist($task);
            $manager->flush();
        }

        for ($i = 1; $i <= 5; $i++) {
            $task = new Task();
            $task->setTitle('task deadline tomorrow n. ' . $i);
            $task->setContent('Deadline tomorrow');
            $task->setDeadline(new DateTime('tomorrow'));
            $manager->persist($task);
            $manager->flush();
        }

        for ($i = 1; $i <= 5; $i++) {
            $task = new Task();
            $task->setTitle('task far away n. ' . $i);
            $task->setContent('deadline in 5 days');
            $task->setDeadline(new DateTime('+5 days'));
            $manager->persist($task);
            $manager->flush();
        }

        for ($i = 1; $i <= 5; $i++) {
            $task = new Task();
            $task->setTitle('task overdue n. ' . $i);
            $task->setContent('This task is overdue');
            $task->setDeadline(new DateTime('-5 days'));
            $manager->persist($task);
            $manager->flush();
        }
    }
    // @codeCoverageIgnoreEnd
}
