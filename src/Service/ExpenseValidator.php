<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Expense;

/**
 * Server-side validation and sanitization for expense input.
 */
class ExpenseValidator
{
    /** @var list<string> */
    private array $allowedCategories;

    /**
     * @param list<string> $allowedCategories
     */
    public function __construct(array $allowedCategories)
    {
        $this->allowedCategories = $allowedCategories;
    }

    /**
     * @return array{errors: list<string>, expense: ?Expense}
     */
    public function validate(string $date, string $category, string $item, string $amount): array
    {
        $errors = [];

        $parsedDate = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if ($parsedDate === false || $parsedDate->format('Y-m-d') !== $date) {
            $errors[] = 'Enter a valid date.';
        }

        if (!in_array($category, $this->allowedCategories, true)) {
            $errors[] = 'Select a valid category.';
        }

        $cleanItem = $this->sanitizeItem($item);
        if ($cleanItem === '') {
            $errors[] = 'Enter an item name.';
        } elseif (strlen($cleanItem) > 100) {
            $errors[] = 'Item name must be 100 characters or fewer.';
        }

        if (!is_numeric($amount) || (float) $amount <= 0) {
            $errors[] = 'Enter an amount greater than zero.';
        }

        if ($errors !== []) {
            return ['errors' => $errors, 'expense' => null];
        }

        /** @var \DateTimeImmutable $parsedDate */
        return [
            'errors' => [],
            'expense' => new Expense(
                $parsedDate,
                $category,
                $cleanItem,
                round((float) $amount, 2)
            ),
        ];
    }

    public function sanitizeItem(string $item): string
    {
        $item = str_replace(['|', "\r", "\n"], ' ', $item);
        $item = preg_replace('/\s+/', ' ', $item) ?? '';

        return trim($item);
    }
}
