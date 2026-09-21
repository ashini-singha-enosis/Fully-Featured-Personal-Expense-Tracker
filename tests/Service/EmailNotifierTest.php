<?php

declare(strict_types=1);

namespace Tests\Service;

use App\Service\EmailNotifier;
use App\Service\Mail\MailerInterface;
use PHPUnit\Framework\TestCase;

final class EmailNotifierTest extends TestCase
{
    public function testNotifyIfOverLimitSendsMail(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())
            ->method('send')
            ->with(
                'admin@example.com',
                self::stringContains('Expense limit alert'),
                self::stringContains('1000.00')
            )
            ->willReturn(true);

        $notifier = new EmailNotifier($mailer, 'admin@example.com', 'Tracker');
        self::assertTrue($notifier->notifyIfOverLimit(1000.0, 1000.0));
    }

    public function testNotifyIfOverLimitSkipsWhenUnderLimit(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::never())->method('send');

        $notifier = new EmailNotifier($mailer, 'admin@example.com', 'Tracker');
        self::assertFalse($notifier->notifyIfOverLimit(50.0, 100.0));
    }

    public function testSendMonthlySummaryBuildsBody(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())
            ->method('send')
            ->with(
                'admin@example.com',
                self::stringContains('Monthly summary'),
                self::stringContains('January 2026')
            )
            ->willReturn(true);

        $notifier = new EmailNotifier($mailer, 'admin@example.com', 'Tracker');
        $sent = $notifier->sendMonthlySummary([
            ['month' => '2026-01', 'label' => 'January 2026', 'total' => 40.0, 'count' => 2],
        ]);

        self::assertTrue($sent);
    }
}
