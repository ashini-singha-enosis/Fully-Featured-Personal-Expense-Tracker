<?php

declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Expense;
use App\Repository\ExpenseRepository;
use App\Service\EmailNotifier;
use App\Service\ReportGenerator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ReportControllerTest extends TestCase
{
    public function testMonthlySummaryUsesReportAndNotifier(): void
    {
        $expenses = [
            new Expense(new DateTimeImmutable('2026-01-10'), 'Food', 'A', 10.0),
        ];

        $repository = $this->createMock(ExpenseRepository::class);
        $repository->method('findAllOrdered')->willReturn($expenses);

        $generator = new ReportGenerator();
        $monthly = $generator->monthlyReport($repository->findAllOrdered());

        $notifier = $this->createMock(EmailNotifier::class);
        $notifier->expects(self::once())
            ->method('sendMonthlySummary')
            ->with($monthly)
            ->willReturn(true);

        self::assertTrue($notifier->sendMonthlySummary($monthly));
        self::assertSame('2026-01', $monthly[0]['month']);
    }
}
