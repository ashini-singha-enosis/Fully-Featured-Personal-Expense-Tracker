<?php

declare(strict_types=1);

namespace Tests\Service;

use App\Service\ExpenseValidator;
use PHPUnit\Framework\TestCase;

final class ExpenseValidatorTest extends TestCase
{
    private ExpenseValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new ExpenseValidator(['Food', 'Transport', 'Other']);
    }

    public function testValidExpensePasses(): void
    {
        $result = $this->validator->validate('2026-09-20', 'Food', 'Lunch', '12.50');

        self::assertSame([], $result['errors']);
        self::assertNotNull($result['expense']);
        self::assertSame('Lunch', $result['expense']->getItem());
        self::assertSame(12.50, $result['expense']->getAmount());
    }

    public function testInvalidDateFails(): void
    {
        $result = $this->validator->validate('20-09-2026', 'Food', 'Lunch', '10');

        self::assertContains('Enter a valid date.', $result['errors']);
        self::assertNull($result['expense']);
    }

    public function testInvalidCategoryFails(): void
    {
        $result = $this->validator->validate('2026-09-20', 'Travel', 'Taxi', '10');

        self::assertContains('Select a valid category.', $result['errors']);
    }

    public function testEmptyItemFails(): void
    {
        $result = $this->validator->validate('2026-09-20', 'Food', '   ', '10');

        self::assertContains('Enter an item name.', $result['errors']);
    }

    public function testNonPositiveAmountFails(): void
    {
        $result = $this->validator->validate('2026-09-20', 'Food', 'Snack', '0');

        self::assertContains('Enter an amount greater than zero.', $result['errors']);
    }

    public function testSanitizeItemRemovesDelimiters(): void
    {
        self::assertSame('Milk Bread', $this->validator->sanitizeItem("Milk|Bread\n"));
    }
}
