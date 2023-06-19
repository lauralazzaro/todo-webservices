<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testEntityTask()
    {
        // Arrange
        $task = new Task();
        $createdAT = new \DateTimeImmutable();
        $title = 'New super cool task';
        $content = 'This is a test for the task entity';

        // Act
        $task->setCreatedAt($createdAT);
        $task->setTitle($title);
        $task->setContent($content);
        $task->setDone(true);

        // Assert
        $this->assertSame($createdAT, $task->getCreatedAt());
        $this->assertEquals($title, $task->getTitle());
        $this->assertEquals($content, $task->getContent());

        $this->assertIsString($task->getTitle());
        $this->assertIsString($task->getContent());
        $this->assertIsBool($task->isDone());
    }
}