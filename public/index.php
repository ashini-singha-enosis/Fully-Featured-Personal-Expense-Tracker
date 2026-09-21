<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\ExpenseController;
use App\Controller\ReportController;

session_start();

require_once dirname(__DIR__) . '/src/bootstrap.php';

$container = createAppContainer();

$page = trim((string) ($_GET['page'] ?? 'expenses'));
if ($page === '') {
    $page = 'expenses';
}

$publicPages = ['login'];

if (!isset($_SESSION['user_id']) && !in_array($page, $publicPages, true)) {
    header('Location: index.php?page=login');
    exit;
}

if (isset($_SESSION['user_id']) && $page === 'login') {
    header('Location: index.php?page=expenses');
    exit;
}

/** @var AuthController $authController */
$authController = $container->get(AuthController::class);
/** @var ExpenseController $expenseController */
$expenseController = $container->get(ExpenseController::class);
/** @var ReportController $reportController */
$reportController = $container->get(ReportController::class);

switch ($page) {
    case 'login':
        $authController->login();
        break;

    case 'logout':
        $authController->logout();
        break;

    case 'expenses/create':
        $expenseController->create();
        break;

    case 'expenses/edit':
        $expenseController->edit();
        break;

    case 'expenses/delete':
        $expenseController->delete();
        break;

    case 'reports':
        $reportController->index();
        break;

    case 'email/monthly-summary':
        $reportController->sendMonthlySummary();
        break;

    case 'expenses':
    default:
        $expenseController->index();
        break;
}
