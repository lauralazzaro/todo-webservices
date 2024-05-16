<?php

namespace App\Tests\Unit;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class TaskTest extends TestCase
{
    private function createMockUser(): User
    {
        $user = $this->getMockBuilder(User::class)
            ->getMock();

        $user->method('getId')->willReturn(1);
        $user->method('getUsername')->willReturn('testuser');
        $user->method('getEmail')->willReturn('test@example.com');
        $user->method('getPassword')->willReturn('password123');

        return $user;
    }

    public function testTitleIsRequired(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $task = new Task();
        $task->setContent('Some content');

        $user = $this->createMockUser();
        $task->setUser($user);

        $violations = $validator->validate($task);
        $this->assertCount(1, $violations);
        $this->assertEquals('You must enter a title.', $violations[0]->getMessage());
    }

    public function testContentIsRequired(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $task = new Task();
        $task->setTitle('Task title');

        $user = $this->createMockUser();
        $task->setUser($user);

        $violations = $validator->validate($task);

        $this->assertCount(1, $violations);
        $this->assertEquals('You must enter a content.', $violations[0]->getMessage());
    }

    public function testUserIsRequired(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $task = new Task();
        $task->setTitle('Task title');
        $task->setContent('Some content');

        $violations = $validator->validate($task);

        $this->assertCount(1, $violations);
        $this->assertEquals('You must assign a user.', $violations[0]->getMessage());
    }

    public function testValidTask(): void
    {
        $validator = Validation::createValidator();
        $task = new Task();
        $task->setTitle('Task title');
        $task->setContent('Some content');
        $task->toggle(true);

        $user = $this->createMockUser();
        $task->setUser($user);

        $violations = $validator->validate($task);
        $this->assertCount(0, $violations);
    }
}
