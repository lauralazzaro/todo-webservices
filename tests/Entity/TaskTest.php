<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class TaskTest extends TestCase
{

    public function testTitleIsRequired()
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();
        $task = new Task();
        $task->setContent('Some content');
        $task->setDone(false);

        $violations = $validator->validate($task);
        $this->assertCount(1, $violations);
        $this->assertEquals('You must enter a title.', $violations[0]->getMessage());
    }

    public function testContentIsRequired()
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();
        
        $task = new Task();
        $task->setTitle('Task title');
        $task->setDone(false);

        $violations = $validator->validate($task);

        $this->assertCount(1, $violations);
        $this->assertEquals('You must enter a content.', $violations[0]->getMessage());
    }

    public function testValidTask()
    {
        $validator = Validation::createValidator();
        $task = new Task();
        $task->setTitle('Task title');
        $task->setContent('Some content');
        $task->setDone(false);

        $violations = $validator->validate($task);
        $this->assertCount(0, $violations);
    }
}