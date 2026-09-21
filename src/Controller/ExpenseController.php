<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Helper;
use App\Entity\Expense;
use App\Repository\ExpenseRepository;
use App\Service\EmailNotifier;
use App\Service\ExpenseValidator;
use DateTimeImmutable;

class ExpenseController
{
    /** @param array<string, mixed> $config */
    public function __construct(
        private array $config,
        private ExpenseRepository $expenseRepository,
        private ExpenseValidator $validator,
        private EmailNotifier $emailNotifier,
    ) {
    }

    public function index(): void
    {
        $expenses = $this->expenseRepository->findAllOrdered();
        $total = $this->expenseRepository->calculateTotal($expenses);
        $flash = (string) ($_SESSION['flash'] ?? '');
        unset($_SESSION['flash']);

        $this->render('expenses/index.php', [
            'pageTitle' => 'Expenses',
            'currentPage' => 'expenses',
            'hideNav' => false,
            'expenses' => $expenses,
            'total' => $total,
            'flash' => $flash,
        ]);
    }

    public function create(): void
    {
        /** @var list<string> $categories */
        $categories = $this->config['categories'];

        $errors = [];
        $form = [
            'date' => date('Y-m-d'),
            'category' => $categories[0] ?? 'Other',
            'item' => '',
            'amount' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $form['date'] = trim((string) ($_POST['date'] ?? ''));
            $form['category'] = trim((string) ($_POST['category'] ?? ''));
            $form['item'] = trim((string) ($_POST['item'] ?? ''));
            $form['amount'] = trim((string) ($_POST['amount'] ?? ''));

            $result = $this->validator->validate(
                $form['date'],
                $form['category'],
                $form['item'],
                $form['amount']
            );

            if ($result['errors'] !== []) {
                $errors = $result['errors'];
            } elseif ($result['expense'] === null) {
                $errors[] = 'Could not process the expense.';
            } else {
                $this->expenseRepository->add($result['expense']);
                $this->emailNotifier->notifyIfOverLimit(
                    $this->expenseRepository->sumAll(),
                    (float) $this->config['expense_limit']
                );
                $_SESSION['flash'] = 'Expense saved successfully.';
                header('Location: ' . Helper::url('expenses'));
                exit;
            }
        }

        $this->render('expenses/create.php', [
            'pageTitle' => 'Add Expense',
            'currentPage' => 'expenses/create',
            'hideNav' => false,
            'categories' => $categories,
            'errors' => $errors,
            'form' => $form,
        ]);
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $expense = $id > 0 ? $this->expenseRepository->findById($id) : null;
        if ($expense === null) {
            $_SESSION['flash'] = 'Expense not found.';
            header('Location: ' . Helper::url('expenses'));
            exit;
        }

        /** @var list<string> $categories */
        $categories = $this->config['categories'];
        $errors = [];
        $form = [
            'date' => $expense->getDate(),
            'category' => $expense->getCategory(),
            'item' => $expense->getItem(),
            'amount' => (string) $expense->getAmount(),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $form['date'] = trim((string) ($_POST['date'] ?? ''));
            $form['category'] = trim((string) ($_POST['category'] ?? ''));
            $form['item'] = trim((string) ($_POST['item'] ?? ''));
            $form['amount'] = trim((string) ($_POST['amount'] ?? ''));

            $result = $this->validator->validate(
                $form['date'],
                $form['category'],
                $form['item'],
                $form['amount']
            );

            if ($result['errors'] !== []) {
                $errors = $result['errors'];
            } elseif ($result['expense'] === null) {
                $errors[] = 'Could not process the expense.';
            } else {
                $this->applyValidatedExpense($expense, $result['expense']);
                $this->expenseRepository->update($expense);
                $this->emailNotifier->notifyIfOverLimit(
                    $this->expenseRepository->sumAll(),
                    (float) $this->config['expense_limit']
                );
                $_SESSION['flash'] = 'Expense updated successfully.';
                header('Location: ' . Helper::url('expenses'));
                exit;
            }
        }

        $this->render('expenses/edit.php', [
            'pageTitle' => 'Edit Expense',
            'currentPage' => 'expenses/edit',
            'hideNav' => false,
            'categories' => $categories,
            'errors' => $errors,
            'form' => $form,
            'expenseId' => $expense->getId(),
        ]);
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . Helper::url('expenses'));
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $expense = $id > 0 ? $this->expenseRepository->findById($id) : null;
        if ($expense !== null) {
            $this->expenseRepository->remove($expense);
            $_SESSION['flash'] = 'Expense deleted.';
        } else {
            $_SESSION['flash'] = 'Expense not found.';
        }

        header('Location: ' . Helper::url('expenses'));
        exit;
    }

    private function applyValidatedExpense(Expense $target, Expense $source): void
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $source->getDate());
        if ($date === false) {
            return;
        }

        $target->setDate($date);
        $target->setCategory($source->getCategory());
        $target->setItem($source->getItem());
        $target->setAmount($source->getAmount());
    }

    /**
     * @param array<string, mixed> $vars
     */
    private function render(string $view, array $vars): void
    {
        $appName = (string) $this->config['app_name'];
        $viewContent = (string) $this->config['paths']['views'] . '/' . $view;
        extract($vars, EXTR_SKIP);
        require (string) $this->config['paths']['views'] . '/layout.php';
    }
}
