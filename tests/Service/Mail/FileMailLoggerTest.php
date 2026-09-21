<?php

declare(strict_types=1);

namespace Tests\Service\Mail;

use App\Service\Mail\FileMailLogger;
use PHPUnit\Framework\TestCase;

final class FileMailLoggerTest extends TestCase
{
    public function testSendAppendsToLogFile(): void
    {
        $path = sys_get_temp_dir() . '/expense_mail_' . uniqid('', true) . '.log';
        $logger = new FileMailLogger($path);

        self::assertTrue($logger->send('to@example.com', 'Hello', 'Body text'));
        self::assertFileExists($path);

        $contents = (string) file_get_contents($path);
        self::assertStringContainsString('to@example.com', $contents);
        self::assertStringContainsString('Hello', $contents);
        self::assertStringContainsString('Body text', $contents);

        unlink($path);
    }
}
