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
            ->enableAnnotationMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();
        $user = new User();
        $user->setEmail('testemail@example.com');
        $user->setPassword('P@sswoRd-123#');
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        $violations = $validator->validate($user);
        $this->assertCount(1, $violations);
        $this->assertEquals('You must enter a username.', $violations[0]->getMessage());
    }

    public function testEmailIsRequired()
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();
        $user = new User();
        $user->setUsername('test_user');
        $user->setPassword('password');

        $violations = $validator->validate($user);
        $this->assertCount(1, $violations);
        $this->assertEquals('You must enter a valid email.', $violations[0]->getMessage());
    }

    public function testEmailFormatIsValid()
    {
        $user = new User();
        $user->setUsername('test_user');
        $user->setEmail('invalid_email');
        $user->setPassword('password');

        $emailInvalidMessage = $user->isEmailValid($user->getEmail());
        $this->assertEquals('The email "invalid_email" is not a valid email.', $emailInvalidMessage);
    }

    public function testPasswordIsRequired()
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();
        $user = new User();
        $user->setUsername('test_user');
        $user->setEmail('test@example.com');

        $violations = $validator->validate($user);
        $this->assertCount(1, $violations);
        $this->assertEquals('You must enter a password.', $violations[0]->getMessage());
    }

    public function testValidUser()
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();
        $user = new User();
        $user->setUsername('test_user');
        $user->setEmail('test@example.com');
        $user->setPassword('password');

        $violations = $validator->validate($user);
        $this->assertCount(0, $violations);
    }
}
