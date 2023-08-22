<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    // @codeCoverageIgnoreStart
    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(
        ObjectManager $manager
    ): void {
        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@email.com');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            '123'
        );
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);

        $manager->persist($user);
        $manager->flush();

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@email.com');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            '123'
        );
        $admin->setPassword($hashedPassword);
        $admin->setRoles(['ROLE_ADMIN','ROLE_USER']);

        $manager->persist($admin);
        $manager->flush();

        $anonymous = new User();
        $anonymous->setUsername('anonymous');
        $anonymous->setEmail('anonymous@email.com');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $anonymous,
            '123'
        );
        $anonymous->setPassword($hashedPassword);
        $anonymous->setRoles([]);

        $manager->persist($anonymous);
        $manager->flush();

        $task = new Task();
        $task->setTitle('Title task 1');
        $task->setContent('This task has been created by an admin');
        $task->setUser($admin);
        $manager->persist($task);
        $manager->flush();

        $task = new Task();
        $task->setTitle('Title task 2');
        $task->setContent('This task has been created by a user');
        $task->setUser($user);
        $manager->persist($task);
        $manager->flush();

        $task = new Task();
        $task->setTitle('Title task 3');
        $task->setContent('This task has been created by anonymous');
        $task->setUser($anonymous);
        $manager->persist($task);
        $manager->flush();
    }
    // @codeCoverageIgnoreEnd
}
