<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\User;

class UserTest extends TestCase
{
    public function testEntityUser()
    {
        // Arrange
        $user = new User();
        $email = 'john.doe@example.com';
        $username = 'john_doe';
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $password = 'secret';

        // Act
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setRoles($roles);
        $user->setPassword($password);

        // Assert
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($roles, $user->getRoles());
        $this->assertEquals($password, $user->getPassword());

        $this->assertIsString($user->getEmail());
        $this->assertIsString($user->getUsername());
        $this->assertIsArray($user->getRoles());
        $this->assertIsString($user->getPassword());
    }




}
