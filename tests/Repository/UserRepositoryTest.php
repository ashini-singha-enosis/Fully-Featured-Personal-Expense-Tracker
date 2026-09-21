<?php

declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class UserRepositoryTest extends TestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        $config = require dirname(__DIR__, 2) . '/config/config.php';
        $config['db'] = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];

        $container = createAppContainer($config);
        $em = $container->get(EntityManagerInterface::class);
        $this->repository = $container->get(UserRepository::class);

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $schemaTool->createSchema([
            $em->getClassMetadata(\App\Entity\Expense::class),
            $em->getClassMetadata(\App\Entity\User::class),
        ]);
    }

    public function testAuthenticateSucceedsWithCorrectPassword(): void
    {
        $hash = password_hash('password123', PASSWORD_DEFAULT);
        $this->repository->add(new User('admin', $hash));

        $user = $this->repository->authenticate('admin', 'password123');
        self::assertNotNull($user);
        self::assertSame('admin', $user->getUsername());
    }

    public function testAuthenticateFailsWithWrongPassword(): void
    {
        $hash = password_hash('password123', PASSWORD_DEFAULT);
        $this->repository->add(new User('admin', $hash));

        self::assertNull($this->repository->authenticate('admin', 'wrong'));
    }
}
