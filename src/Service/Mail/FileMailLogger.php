<?php

declare(strict_types=1);

namespace App\Service\Mail;

/**
 * Writes outbound email content to a log file when SMTP is not configured.
 */
class FileMailLogger implements MailerInterface
{
    public function __construct(private string $logFile)
    {
    }

    public function send(string $to, string $subject, string $body): bool
    {
        $directory = dirname($this->logFile);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            return false;
        }

        $entry = sprintf(
            "[%s]\nTo: %s\nSubject: %s\n%s\n%s\n\n",
            date('c'),
            $to,
            $subject,
            str_repeat('-', 40),
            $body
        );

        return file_put_contents($this->logFile, $entry, FILE_APPEND | LOCK_EX) !== false;
    }

    public function getLogFile(): string
    {
        return $this->logFile;
    }
}
