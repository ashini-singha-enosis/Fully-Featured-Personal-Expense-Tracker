<?php

declare(strict_types=1);

use App\Controller\Api\ExpenseApiController;

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/src/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'errors' => ['Authentication required. Log in via the web app first.'],
    ], JSON_THROW_ON_ERROR);
    exit;
}

$container = createAppContainer();

$resource = trim((string) ($_GET['resource'] ?? 'expenses'));
$idParam = $_GET['id'] ?? null;
$id = $idParam !== null && $idParam !== '' ? (int) $idParam : null;

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

if ($resource !== 'expenses') {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'errors' => ['Unknown resource.'],
    ], JSON_THROW_ON_ERROR);
    exit;
}

/** @var ExpenseApiController $api */
$api = $container->get(ExpenseApiController::class);
$api->handle($method, $id);
