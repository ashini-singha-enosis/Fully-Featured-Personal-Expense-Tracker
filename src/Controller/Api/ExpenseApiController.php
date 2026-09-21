<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Expense;
use App\Repository\ExpenseRepository;
use App\Service\EmailNotifier;
use App\Service\ExpenseValidator;
use DateTimeImmutable;

class ExpenseApiController
{
    /** @param array<string, mixed> $config */
    public function __construct(
        private array $config,
        private ExpenseRepository $expenseRepository,
        private ExpenseValidator $validator,
        private EmailNotifier $emailNotifier,
    ) {
    }

    public function handle(string $method, ?int $id): void
    {
        match ($method) {
            'GET' => $id === null ? $this->list() : $this->show($id),
            'POST' => $this->create(),
            'PUT', 'PATCH' => $this->update($id),
            'DELETE' => $this->delete($id),
            default => $this->json(['success' => false, 'errors' => ['Method not allowed.']], 405),
        };
    }

    private function list(): void
    {
        $data = array_map(
            static fn (Expense $expense): array => $expense->toArray(),
            $this->expenseRepository->findAllOrdered()
        );

        $this->json(['success' => true, 'data' => $data]);
    }

    private function show(int $id): void
    {
        $expense = $this->expenseRepository->findById($id);
        if ($expense === null) {
            $this->json(['success' => false, 'errors' => ['Expense not found.']], 404);
            return;
        }

        $this->json(['success' => true, 'data' => $expense->toArray()]);
    }

    private function create(): void
    {
        $payload = $this->readJsonBody();
        $result = $this->validator->validate(
            (string) ($payload['date'] ?? ''),
            (string) ($payload['category'] ?? ''),
            (string) ($payload['item'] ?? ''),
            (string) ($payload['amount'] ?? '')
        );

        if ($result['errors'] !== [] || $result['expense'] === null) {
            $this->json(['success' => false, 'errors' => $result['errors'] ?: ['Invalid payload.']], 422);
            return;
        }

        $this->expenseRepository->add($result['expense']);
        $this->emailNotifier->notifyIfOverLimit(
            $this->expenseRepository->sumAll(),
            (float) $this->config['expense_limit']
        );

        $this->json(['success' => true, 'data' => $result['expense']->toArray()], 201);
    }

    private function update(?int $id): void
    {
        if ($id === null || $id <= 0) {
            $this->json(['success' => false, 'errors' => ['Expense id is required.']], 400);
            return;
        }

        $expense = $this->expenseRepository->findById($id);
        if ($expense === null) {
            $this->json(['success' => false, 'errors' => ['Expense not found.']], 404);
            return;
        }

        $payload = $this->readJsonBody();
        $result = $this->validator->validate(
            (string) ($payload['date'] ?? ''),
            (string) ($payload['category'] ?? ''),
            (string) ($payload['item'] ?? ''),
            (string) ($payload['amount'] ?? '')
        );

        if ($result['errors'] !== [] || $result['expense'] === null) {
            $this->json(['success' => false, 'errors' => $result['errors'] ?: ['Invalid payload.']], 422);
            return;
        }

        $source = $result['expense'];
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $source->getDate());
        if ($date === false) {
            $this->json(['success' => false, 'errors' => ['Invalid date.']], 422);
            return;
        }

        $expense->setDate($date);
        $expense->setCategory($source->getCategory());
        $expense->setItem($source->getItem());
        $expense->setAmount($source->getAmount());
        $this->expenseRepository->update($expense);
        $this->emailNotifier->notifyIfOverLimit(
            $this->expenseRepository->sumAll(),
            (float) $this->config['expense_limit']
        );

        $this->json(['success' => true, 'data' => $expense->toArray()]);
    }

    private function delete(?int $id): void
    {
        if ($id === null || $id <= 0) {
            $this->json(['success' => false, 'errors' => ['Expense id is required.']], 400);
            return;
        }

        $expense = $this->expenseRepository->findById($id);
        if ($expense === null) {
            $this->json(['success' => false, 'errors' => ['Expense not found.']], 404);
            return;
        }

        $this->expenseRepository->remove($expense);
        $this->json(['success' => true, 'data' => null]);
    }

    /**
     * @return array<string, mixed>
     */
    private function readJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_THROW_ON_ERROR);
    }
}
