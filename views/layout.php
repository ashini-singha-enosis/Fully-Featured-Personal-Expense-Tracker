<?php

declare(strict_types=1);

use App\Core\Helper;

/** @var string $appName */
/** @var string $pageTitle */
/** @var string $viewContent */
/** @var string $currentPage */
/** @var bool $hideNav */

$username = (string) ($_SESSION['username'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helper::e($pageTitle) ?> — <?= Helper::e($appName) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;550;650;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="page-shell">
        <header class="site-header">
            <div class="brand-block">
                <p class="brand-name"><?= Helper::e($appName) ?></p>
                <p class="brand-tag">Doctrine ORM · DI · email · REST API</p>
            </div>

            <?php if (empty($hideNav)): ?>
                <nav class="site-nav" aria-label="Main">
                    <a
                        class="<?= $currentPage === 'expenses' ? 'is-active' : '' ?>"
                        href="<?= Helper::e(Helper::url('expenses')) ?>"
                    >Expenses</a>
                    <a
                        class="<?= $currentPage === 'expenses/create' ? 'is-active' : '' ?>"
                        href="<?= Helper::e(Helper::url('expenses/create')) ?>"
                    >Add expense</a>
                    <a
                        class="<?= $currentPage === 'reports' ? 'is-active' : '' ?>"
                        href="<?= Helper::e(Helper::url('reports')) ?>"
                    >Reports</a>
                    <span class="nav-user">Signed in as <?= Helper::e($username) ?></span>
                    <a class="nav-logout" href="<?= Helper::e(Helper::url('logout')) ?>">Log out</a>
                </nav>
            <?php endif; ?>
        </header>

        <main class="site-main">
            <?php require $viewContent; ?>
        </main>
    </div>
</body>
</html>
