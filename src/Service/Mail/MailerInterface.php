<?php

declare(strict_types=1);

namespace App\Service\Mail;

interface MailerInterface
{
    public function send(string $to, string $subject, string $body): bool;
}
