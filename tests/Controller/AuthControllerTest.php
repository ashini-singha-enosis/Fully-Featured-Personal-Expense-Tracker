<?php

declare(strict_types=1);

namespace Tests\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Auth collaboration: repository authenticate used by AuthController.
 */
final class AuthControllerTest extends TestCase
{
    public function testAuthenticateDelegatesToUserRepository(): void
    {
        $user = new User('admin', password_hash('password123', PASSWORD_DEFAULT));

        $repository = $this->createMock(UserRepository::class);
        $repository->expects(self::once())
            ->method('authenticate')
            ->with('admin', 'password123')
            ->willReturn($user);

        $result = $repository->authenticate('admin', 'password123');
        self::assertSame($user, $result);
        self::assertSame('admin', $result->getUsername());
    }

    public function testAuthenticateReturnsNullForInvalidCredentials(): void
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->method('authenticate')->willReturn(null);

        self::assertNull($repository->authenticate('admin', 'wrong'));
    }
}
