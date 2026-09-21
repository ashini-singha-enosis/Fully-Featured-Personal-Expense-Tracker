<?php

declare(strict_types=1);

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

require_once dirname(__DIR__) . '/src/bootstrap.php';

$container = createAppContainer();

/** @var EntityManagerInterface $em */
$em = $container->get(EntityManagerInterface::class);
/** @var UserRepository $users */
$users = $container->get(UserRepository::class);

if ($users->findByUsername('admin') !== null) {
    echo "Admin user already exists.\n";
    exit(0);
}

$hash = password_hash('password123', PASSWORD_DEFAULT);
$users->add(new User('admin', $hash));

echo "Seeded admin user (admin / password123).\n";
