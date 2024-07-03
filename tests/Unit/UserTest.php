<?php

namespace App\Tests\Unit;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Validation;

class UserTest extends TestCase
{
    public function testUsernameIsRequired(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(
                new ConstraintValidatorFactory([
                    EmailValidator::class => new EmailValidator('html5')
                ])
            )
            ->enableAttributeMapping()
            ->getValidator();

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('123');
        $user->setRoles(['ROLE_ADMIN']);

        $violations = $validator->validate($user);
        $this->assertCount(1, $violations);
    }

    public function testEmailIsRequired(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(
                new ConstraintValidatorFactory([
                    EmailValidator::class => new EmailValidator('html5')
                ])
            )
            ->enableAttributeMapping()
            ->getValidator();

        $user = new User();
        $user->setUsername('test_user');
        $user->setPassword('password');

        $violations = $validator->validate($user);
        $this->assertCount(1, $violations, 'Email is required');
    }

    public function testValidUser(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(
                new ConstraintValidatorFactory([
                    EmailValidator::class => new EmailValidator('html5')
                ])
            )
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
