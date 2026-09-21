<?php

declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\Expense;
use App\Entity\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ExpenseTest extends TestCase
{
    public function testGettersAndFormatters(): void
    {
        $expense = new Expense(new DateTimeImmutable('2026-09-20'), 'Food', 'Lunch', 12.5);

        self::assertNull($expense->getId());
        self::assertSame('2026-09-20', $expense->getDate());
        self::assertSame('Food', $expense->getCategory());
        self::assertSame('Lunch', $expense->getItem());
        self::assertSame(12.5, $expense->getAmount());
        self::assertSame('12.50', $expense->formatAmount());
        self::assertSame(
            [
                'id' => null,
                'date' => '2026-09-20',
                'category' => 'Food',
                'item' => 'Lunch',
                'amount' => 12.5,
            ],
            $expense->toArray()
        );
    }

    public function testSettersUpdateValues(): void
    {
        $expense = new Expense(new DateTimeImmutable('2026-01-01'), 'Food', 'A', 1.0);
        $expense->setDate(new DateTimeImmutable('2026-02-02'));
        $expense->setCategory('Transport');
        $expense->setItem('Bus');
        $expense->setAmount(3.25);

        self::assertSame('2026-02-02', $expense->getDate());
        self::assertSame('Transport', $expense->getCategory());
        self::assertSame('Bus', $expense->getItem());
        self::assertSame(3.25, $expense->getAmount());
    }

    public function testCreateFromParts(): void
    {
        $expense = Expense::createFromParts('2026-09-20', 'Other', 'Item', 9.99);
        self::assertSame('2026-09-20', $expense->getDate());
        self::assertSame(9.99, $expense->getAmount());
    }
}

final class UserTest extends TestCase
{
    public function testVerifyPassword(): void
    {
        $hash = password_hash('secret', PASSWORD_DEFAULT);
        $user = new User('admin', $hash);

        self::assertNull($user->getId());
        self::assertSame('admin', $user->getUsername());
        self::assertTrue($user->verifyPassword('secret'));
        self::assertFalse($user->verifyPassword('wrong'));
    }
}
