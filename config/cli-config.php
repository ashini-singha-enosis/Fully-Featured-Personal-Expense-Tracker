<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/src/bootstrap.php';

$container = createAppContainer();

return ConsoleRunner::createHelperSet($container->get(EntityManagerInterface::class));
