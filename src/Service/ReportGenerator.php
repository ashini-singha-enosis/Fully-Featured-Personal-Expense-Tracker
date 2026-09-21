<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Expense;

/**
 * Builds monthly and category-wise spending summaries.
 */
class ReportGenerator
{
    /**
     * @param list<Expense> $expenses
     * @return list<array{month: string, label: string, total: float, count: int}>
     */
    public function monthlyReport(array $expenses): array
    {
        $groups = [];

        foreach ($expenses as $expense) {
            $month = substr($expense->getDate(), 0, 7);
            if (!isset($groups[$month])) {
                $groups[$month] = [
                    'month' => $month,
                    'label' => $this->formatMonthLabel($month),
                    'total' => 0.0,
                    'count' => 0,
                ];
            }
            $groups[$month]['total'] += $expense->getAmount();
            $groups[$month]['count']++;
        }

        krsort($groups);

        return array_values($groups);
    }

    /**
     * @param list<Expense> $expenses
     * @return list<array{category: string, total: float, count: int, percent: float}>
     */
    public function categoryReport(array $expenses): array
    {
        $groups = [];
        $grandTotal = 0.0;

        foreach ($expenses as $expense) {
            $category = $expense->getCategory();
            if (!isset($groups[$category])) {
                $groups[$category] = [
                    'category' => $category,
                    'total' => 0.0,
                    'count' => 0,
                    'percent' => 0.0,
                ];
            }
            $groups[$category]['total'] += $expense->getAmount();
            $groups[$category]['count']++;
            $grandTotal += $expense->getAmount();
        }

        foreach ($groups as &$row) {
            $row['percent'] = $grandTotal > 0
                ? round(($row['total'] / $grandTotal) * 100, 1)
                : 0.0;
        }
        unset($row);

        uasort($groups, static function (array $a, array $b): int {
            return $b['total'] <=> $a['total'];
        });

        return array_values($groups);
    }

    private function formatMonthLabel(string $month): string
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m', $month);
        if ($date === false) {
            return $month;
        }

        return $date->format('F Y');
    }
}
