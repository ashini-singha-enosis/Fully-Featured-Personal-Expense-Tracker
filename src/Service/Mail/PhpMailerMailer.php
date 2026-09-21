<?php

declare(strict_types=1);

namespace App\Service\Mail;

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Sends email through SMTP using PHPMailer.
 */
class PhpMailerMailer implements MailerInterface
{
    /** @param array<string, mixed> $mailConfig */
    public function __construct(private array $mailConfig)
    {
    }

    public function send(string $to, string $subject, string $body): bool
    {
        $mailer = new PHPMailer(true);

        try {
            $mailer->isSMTP();
            $mailer->Host = (string) $this->mailConfig['host'];
            $mailer->Port = (int) $this->mailConfig['port'];
            $mailer->SMTPAuth = true;
            $mailer->Username = (string) $this->mailConfig['username'];
            $mailer->Password = (string) $this->mailConfig['password'];

            $encryption = (string) ($this->mailConfig['encryption'] ?? '');
            if ($encryption !== '') {
                $mailer->SMTPSecure = $encryption;
            }

            $mailer->setFrom(
                (string) $this->mailConfig['from'],
                (string) ($this->mailConfig['from_name'] ?? 'Expense Tracker')
            );
            $mailer->addAddress($to);
            $mailer->Subject = $subject;
            $mailer->Body = $body;
            $mailer->isHTML(false);

            return $mailer->send();
        } catch (MailException) {
            return false;
        }
    }
}
