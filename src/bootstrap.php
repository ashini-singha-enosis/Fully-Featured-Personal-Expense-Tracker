<?php

declare(strict_types=1);

use App\Controller\Api\ExpenseApiController;
use App\Controller\AuthController;
use App\Controller\ExpenseController;
use App\Controller\ReportController;
use App\Core\Container;
use App\Repository\ExpenseRepository;
use App\Repository\UserRepository;
use App\Service\EmailNotifier;
use App\Service\ExpenseValidator;
use App\Service\Mail\FileMailLogger;
use App\Service\Mail\MailerInterface;
use App\Service\Mail\PhpMailerMailer;
use App\Service\ReportGenerator;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;

require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * @param array<string, mixed>|null $configOverride Used by tests (e.g. SQLite).
 * @return Container
 */
function createAppContainer(?array $configOverride = null): Container
{
    /** @var array<string, mixed> $config */
    $config = $configOverride ?? require dirname(__DIR__) . '/config/config.php';

    $container = new Container();

    $container->set('config', static fn (): array => $config);

    $container->set(EntityManagerInterface::class, static function () use ($config): EntityManagerInterface {
        /** @var array{driver: string, host?: string, port?: int, dbname?: string, user?: string, password?: string, charset?: string, path?: string, memory?: bool} $db */
        $db = $config['db'];

        $ormConfig = ORMSetup::createAttributeMetadataConfiguration(
            paths: [(string) $config['paths']['entities']],
            isDevMode: true,
        );

        $connection = DriverManager::getConnection($db, $ormConfig);

        return new EntityManager($connection, $ormConfig);
    });

    $container->set(ExpenseRepository::class, static function (Container $c): ExpenseRepository {
        return new ExpenseRepository($c->get(EntityManagerInterface::class));
    });

    $container->set(UserRepository::class, static function (Container $c): UserRepository {
        return new UserRepository($c->get(EntityManagerInterface::class));
    });

    $container->set(ExpenseValidator::class, static function (Container $c): ExpenseValidator {
        /** @var array<string, mixed> $cfg */
        $cfg = $c->get('config');
        /** @var list<string> $categories */
        $categories = $cfg['categories'];

        return new ExpenseValidator($categories);
    });

    $container->set(ReportGenerator::class, static fn (): ReportGenerator => new ReportGenerator());

    $container->set(MailerInterface::class, static function (Container $c): MailerInterface {
        /** @var array<string, mixed> $cfg */
        $cfg = $c->get('config');
        /** @var array<string, mixed> $mail */
        $mail = $cfg['mail'];
        $host = trim((string) ($mail['host'] ?? ''));

        if ($host === '') {
            return new FileMailLogger((string) $mail['log_file']);
        }

        return new PhpMailerMailer($mail);
    });

    $container->set(EmailNotifier::class, static function (Container $c): EmailNotifier {
        /** @var array<string, mixed> $cfg */
        $cfg = $c->get('config');
        /** @var array<string, mixed> $mail */
        $mail = $cfg['mail'];

        return new EmailNotifier(
            $c->get(MailerInterface::class),
            (string) $mail['to'],
            (string) $cfg['app_name'],
        );
    });

    $container->set(AuthController::class, static function (Container $c): AuthController {
        return new AuthController(
            $c->get('config'),
            $c->get(UserRepository::class),
        );
    });

    $container->set(ExpenseController::class, static function (Container $c): ExpenseController {
        return new ExpenseController(
            $c->get('config'),
            $c->get(ExpenseRepository::class),
            $c->get(ExpenseValidator::class),
            $c->get(EmailNotifier::class),
        );
    });

    $container->set(ReportController::class, static function (Container $c): ReportController {
        return new ReportController(
            $c->get('config'),
            $c->get(ExpenseRepository::class),
            $c->get(ReportGenerator::class),
            $c->get(EmailNotifier::class),
        );
    });

    $container->set(ExpenseApiController::class, static function (Container $c): ExpenseApiController {
        return new ExpenseApiController(
            $c->get('config'),
            $c->get(ExpenseRepository::class),
            $c->get(ExpenseValidator::class),
            $c->get(EmailNotifier::class),
        );
    });

    return $container;
}
