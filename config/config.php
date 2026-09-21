<?php

declare(strict_types=1);

/**
 * Application configuration.
 *
 * Leave mail.host empty to write emails to storage/mail.log instead of SMTP.
 */
return [
    'app_name' => 'Personal Expense Tracker',
    'expense_limit' => 1000.00,
    'categories' => [
        'Food',
        'Transport',
        'Housing',
        'Utilities',
        'Entertainment',
        'Health',
        'Other',
    ],
    'db' => [
        'driver' => 'pdo_mysql',
        'host' => '127.0.0.1',
        'port' => 3306,
        'dbname' => 'expenses',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    'mail' => [
        'host' => '',
        'port' => 587,
        'username' => '',
        'password' => '',
        'encryption' => 'tls',
        'from' => 'noreply@example.com',
        'from_name' => 'Expense Tracker',
        'to' => 'admin@example.com',
        'log_file' => __DIR__ . '/../storage/mail.log',
    ],
    'paths' => [
        'views' => __DIR__ . '/../views',
        'entities' => __DIR__ . '/../src/Entity',
    ],
];
