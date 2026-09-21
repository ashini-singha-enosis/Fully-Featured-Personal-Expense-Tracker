<?php

declare(strict_types=1);

use App\Core\Helper;

/** @var string $flash */
?>
<?php if ($flash !== ''): ?>
    <p class="message message-success" role="status"><?= Helper::e($flash) ?></p>
<?php endif; ?>
