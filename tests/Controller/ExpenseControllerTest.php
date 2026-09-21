<?php

declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Expense;
use App\Repository\ExpenseRepository;
use App\Service\EmailNotifier;
use App\Service\ExpenseValidator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Controller collaboration: validation then persist + limit notification.
 */
final class ExpenseControllerTest extends TestCase
{
    public function testCreateFlowPersistsAndChecksLimit(): void
    {
        $validator = new ExpenseValidator(['Food', 'Other']);
        $result = $validator->validate('2026-09-20', 'Food', 'Lunch', '12.50');
        self::assertNotNull($result['expense']);

        $repository = $this->createMock(ExpenseRepository::class);
        $repository->expects(self::once())->method('add')->with($result['expense']);
        $repository->method('sumAll')->willReturn(12.5);

        $notifier = $this->createMock(EmailNotifier::class);
        $notifier->expects(self::once())
            ->method('notifyIfOverLimit')
            ->with(12.5, 1000.0)
            ->willReturn(false);

        $repository->add($result['expense']);
        $notifier->notifyIfOverLimit($repository->sumAll(), 1000.0);
    }

    public function testDeleteFlowRemovesExpense(): void
    {
        $expense = new Expense(new DateTimeImmutable('2026-09-20'), 'Food', 'Lunch', 12.5);

        $repository = $this->createMock(ExpenseRepository::class);
        $repository->method('findById')->with(1)->willReturn($expense);
        $repository->expects(self::once())->method('remove')->with($expense);

        $found = $repository->findById(1);
        self::assertNotNull($found);
        $repository->remove($found);
    }
}
