<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class UserTest extends TestCase
{

    public function testUsernameIsRequired()
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('123');
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $user->setIsPasswordGenerated(true);

        $violations = $validator->validate($user);
        $this->assertCount(1, $violations);
    }

    public function testEmailIsRequired()
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $user = new User();
        $user->setUsername('test_user');
        $user->setPassword('password');

        $violations = $validator->validate($user);
        $this->assertCount(1, $violations, 'Email is required');
    }

    public function testValidUser()
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $user = new User();
        $user->setUsername('test_user');
        $user->setEmail('test@example.com');
        $user->setPassword('password');

        $violations = $validator->validate($user);
        $this->assertCount(0, $violations, 'Invalid User');
    }
}
