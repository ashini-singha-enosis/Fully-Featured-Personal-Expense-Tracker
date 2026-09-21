<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Mail\MailerInterface;

/**
 * Application email notifications (monthly summary and over-limit alerts).
 */
class EmailNotifier
{
    public function __construct(
        private MailerInterface $mailer,
        private string $recipient,
        private string $appName,
    ) {
    }

    /**
     * @param list<array{month: string, label: string, total: float, count: int}> $monthlyReportRows
     */
    public function sendMonthlySummary(array $monthlyReportRows): bool
    {
        $lines = [$this->appName . ' — Monthly expense summary', ''];

        if ($monthlyReportRows === []) {
            $lines[] = 'No expenses recorded yet.';
        } else {
            foreach ($monthlyReportRows as $row) {
                $lines[] = sprintf(
                    '%s: %d entries, total %.2f',
                    $row['label'],
                    $row['count'],
                    $row['total']
                );
            }
        }

        return $this->mailer->send(
            $this->recipient,
            $this->appName . ' — Monthly summary',
            implode("\n", $lines)
        );
    }

    public function notifyIfOverLimit(float $currentTotal, float $limit): bool
    {
        if ($currentTotal < $limit) {
            return false;
        }

        $body = sprintf(
            "Your total expenses (%.2f) have reached or exceeded the configured limit of %.2f.",
            $currentTotal,
            $limit
        );

        return $this->mailer->send(
            $this->recipient,
            $this->appName . ' — Expense limit alert',
            $body
        );
    }
}
