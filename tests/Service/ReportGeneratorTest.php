<?php

declare(strict_types=1);

namespace Tests\Service;

use App\Entity\Expense;
use App\Service\ReportGenerator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ReportGeneratorTest extends TestCase
{
    public function testMonthlyReportGroupsByMonth(): void
    {
        $generator = new ReportGenerator();
        $expenses = [
            new Expense(new DateTimeImmutable('2026-01-10'), 'Food', 'A', 10.0),
            new Expense(new DateTimeImmutable('2026-01-15'), 'Food', 'B', 5.0),
            new Expense(new DateTimeImmutable('2026-02-01'), 'Transport', 'C', 20.0),
        ];

        $monthly = $generator->monthlyReport($expenses);

        self::assertCount(2, $monthly);
        self::assertSame('2026-02', $monthly[0]['month']);
        self::assertSame(20.0, $monthly[0]['total']);
        self::assertSame('2026-01', $monthly[1]['month']);
        self::assertSame(15.0, $monthly[1]['total']);
        self::assertSame(2, $monthly[1]['count']);
    }

    public function testCategoryReportIncludesPercent(): void
    {
        $generator = new ReportGenerator();
        $expenses = [
            new Expense(new DateTimeImmutable('2026-01-10'), 'Food', 'A', 75.0),
            new Expense(new DateTimeImmutable('2026-01-11'), 'Transport', 'B', 25.0),
        ];

        $byCategory = $generator->categoryReport($expenses);

        self::assertSame('Food', $byCategory[0]['category']);
        self::assertSame(75.0, $byCategory[0]['percent']);
        self::assertSame('Transport', $byCategory[1]['category']);
        self::assertSame(25.0, $byCategory[1]['percent']);
    }
}
