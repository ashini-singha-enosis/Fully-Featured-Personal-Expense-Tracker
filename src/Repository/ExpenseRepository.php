<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Expense;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<Expense>
 */
class ExpenseRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Expense::class));
    }

    /**
     * @return list<Expense>
     */
    public function findAllOrdered(): array
    {
        /** @var list<Expense> $expenses */
        $expenses = $this->findBy([], ['date' => 'DESC', 'id' => 'DESC']);

        return $expenses;
    }

    public function findById(int $id): ?Expense
    {
        return $this->find($id);
    }

    public function add(Expense $expense): void
    {
        $this->getEntityManager()->persist($expense);
        $this->getEntityManager()->flush();
    }

    public function update(Expense $expense): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Expense $expense): void
    {
        $this->getEntityManager()->remove($expense);
        $this->getEntityManager()->flush();
    }

    /**
     * @param list<Expense> $expenses
     */
    public function calculateTotal(array $expenses): float
    {
        $total = 0.0;
        foreach ($expenses as $expense) {
            $total += $expense->getAmount();
        }

        return round($total, 2);
    }

    public function sumAll(): float
    {
        $result = $this->createQueryBuilder('e')
            ->select('COALESCE(SUM(e.amount), 0)')
            ->getQuery()
            ->getSingleScalarResult();

        return round((float) $result, 2);
    }
}
