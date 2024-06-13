<?php

namespace App\Tests\Unit;

use App\Entity\Task;
use App\Entity\User;
use App\Validator\DeadlineInFutureValidator;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskTest extends TestCase
{
    private ValidatorInterface $validator;

    public function testTitleIsRequired(): void
    {
        $task = new Task();
        $task->setContent('Some content');

        $user = $this->createMockUser();
        $task->setUser($user);

        $violations = $this->validator->validate($task);
        $this->assertCount(1, $violations);
        $this->assertEquals('You must enter a title.', $violations[0]->getMessage());
    }

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

    public function testContentIsRequired(): void
    {
        $task = new Task();
        $task->setTitle('Task title');

        $user = $this->createMockUser();
        $task->setUser($user);

        $violations = $this->validator->validate($task);

        $this->assertCount(1, $violations);
        $this->assertEquals('You must enter a content.', $violations[0]->getMessage());
    }

    public function testValidTask(): void
    {
        $task = new Task();
        $task->setTitle('Task title');
        $task->setContent('Some content');

        $user = $this->createMockUser();
        $task->setUser($user);

        $violations = $this->validator->validate($task);
        $this->assertCount(0, $violations);
    }

    public function testSetDeadlineInThePast(): void
    {
        $yesterday = new DateTime('-1 day');

        $task = new Task();
        $task->setTitle('Task title');
        $task->setContent('Some content');
        $task->setDeadline($yesterday);
        $violations = $this->validator->validate($task);
        $this->assertCount(1, $violations);
    }

    public function testDeadlineWithoutTime(): void
    {
        $task = new Task();
        $task->setTitle('Task title');
        $task->setContent('Some content');

        $hours = $task->getDeadline()->format('H');
        $this->assertEquals(0, $hours, 'Hours should be 0');

        $minutes = $task->getDeadline()->format('i');
        $this->assertEquals(0, $minutes, 'Minutes should be 0');
    }

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(
                new ConstraintValidatorFactory([
                    DeadlineInFutureValidator::class => new DeadlineInFutureValidator()
                ])
            )
            ->enableAttributeMapping()
            ->getValidator();
    }
}
