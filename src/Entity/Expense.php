<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'expenses')]
class Expense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    private DateTimeInterface $date;

    #[ORM\Column(type: 'string', length: 50)]
    private string $category;

    #[ORM\Column(type: 'string', length: 100)]
    private string $item;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $amount;

    public function __construct(
        DateTimeInterface $date,
        string $category,
        string $item,
        float $amount
    ) {
        $this->date = $date;
        $this->category = $category;
        $this->item = $item;
        $this->amount = number_format($amount, 2, '.', '');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): string
    {
        return $this->date->format('Y-m-d');
    }

    public function getDateObject(): DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function getItem(): string
    {
        return $this->item;
    }

    public function setItem(string $item): void
    {
        $this->item = $item;
    }

    public function getAmount(): float
    {
        return (float) $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = number_format($amount, 2, '.', '');
    }

    public function formatAmount(): string
    {
        return number_format($this->getAmount(), 2);
    }

    /**
     * @return array{id: int|null, date: string, category: string, item: string, amount: float}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->getDate(),
            'category' => $this->category,
            'item' => $this->item,
            'amount' => $this->getAmount(),
        ];
    }

    public static function createFromParts(
        string $date,
        string $category,
        string $item,
        float $amount
    ): self {
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if ($parsed === false) {
            throw new \InvalidArgumentException('Invalid date.');
        }

        return new self($parsed, $category, $item, $amount);
    }
}
