<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Helper\UserHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Exception;

class AdminControllerUnitTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testInitUserAdminDataForCreate(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_ADMIN']);

        $passwordHasherMock = $this->createMock(UserPasswordHasherInterface::class);

        $userHelper = new UserHelper($passwordHasherMock);

        $userHelper->initUserData($user);

        $this->assertContains('ROLE_ADMIN', $user->getRoles());
    }
}