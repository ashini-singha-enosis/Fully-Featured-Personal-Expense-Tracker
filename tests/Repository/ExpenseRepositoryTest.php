<?php

declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\Expense;
use App\Repository\ExpenseRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class ExpenseRepositoryTest extends TestCase
{
    private EntityManagerInterface $em;
    private ExpenseRepository $repository;

    protected function setUp(): void
    {
        $config = require dirname(__DIR__, 2) . '/config/config.php';
        $config['db'] = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];

        $container = createAppContainer($config);
        $this->em = $container->get(EntityManagerInterface::class);
        $this->repository = $container->get(ExpenseRepository::class);

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $schemaTool->createSchema([
            $this->em->getClassMetadata(\App\Entity\Expense::class),
            $this->em->getClassMetadata(\App\Entity\User::class),
        ]);
    }

    public function testAddAndFindAllOrdered(): void
    {
        $older = new Expense(new DateTimeImmutable('2026-01-01'), 'Food', 'Old', 5.0);
        $newer = new Expense(new DateTimeImmutable('2026-02-01'), 'Transport', 'New', 10.0);

        $this->repository->add($older);
        $this->repository->add($newer);

        $all = $this->repository->findAllOrdered();
        self::assertCount(2, $all);
        self::assertSame('New', $all[0]->getItem());
        self::assertSame(15.0, $this->repository->sumAll());
    }

    public function testUpdateAndRemove(): void
    {
        $expense = new Expense(new DateTimeImmutable('2026-03-01'), 'Food', 'Tea', 3.0);
        $this->repository->add($expense);

        $id = (int) $expense->getId();
        $loaded = $this->repository->findById($id);
        self::assertNotNull($loaded);

        $loaded->setItem('Coffee');
        $loaded->setAmount(4.5);
        $this->repository->update($loaded);

        $updated = $this->repository->findById($id);
        self::assertNotNull($updated);
        self::assertSame('Coffee', $updated->getItem());
        self::assertSame(4.5, $updated->getAmount());

        $this->repository->remove($updated);
        self::assertNull($this->repository->findById($id));
    }
}
