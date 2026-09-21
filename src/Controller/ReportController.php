<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Helper;
use App\Repository\ExpenseRepository;
use App\Service\EmailNotifier;
use App\Service\ReportGenerator;

class ReportController
{
    /** @param array<string, mixed> $config */
    public function __construct(
        private array $config,
        private ExpenseRepository $expenseRepository,
        private ReportGenerator $reportGenerator,
        private EmailNotifier $emailNotifier,
    ) {
    }

    public function index(): void
    {
        $expenses = $this->expenseRepository->findAllOrdered();
        $monthly = $this->reportGenerator->monthlyReport($expenses);
        $byCategory = $this->reportGenerator->categoryReport($expenses);
        $grandTotal = $this->expenseRepository->calculateTotal($expenses);
        $flash = (string) ($_SESSION['flash'] ?? '');
        unset($_SESSION['flash']);

        $this->render('reports/index.php', [
            'pageTitle' => 'Reports',
            'currentPage' => 'reports',
            'hideNav' => false,
            'monthly' => $monthly,
            'byCategory' => $byCategory,
            'grandTotal' => $grandTotal,
            'flash' => $flash,
        ]);
    }

    public function sendMonthlySummary(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . Helper::url('reports'));
            exit;
        }

        $expenses = $this->expenseRepository->findAllOrdered();
        $monthly = $this->reportGenerator->monthlyReport($expenses);
        $sent = $this->emailNotifier->sendMonthlySummary($monthly);

        $_SESSION['flash'] = $sent
            ? 'Monthly summary email sent (or written to mail.log).'
            : 'Could not send the monthly summary email.';

        header('Location: ' . Helper::url('reports'));
        exit;
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
