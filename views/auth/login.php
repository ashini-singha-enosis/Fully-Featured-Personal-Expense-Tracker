<?php

declare(strict_types=1);

use App\Core\Helper;

/** @var list<string> $errors */
/** @var string $username */
?>
<section class="auth-panel" aria-labelledby="login-heading">
    <h1 id="login-heading">Sign in</h1>
    <p class="panel-note">Use your account to access the expense tracker.</p>

    <?php if ($errors !== []): ?>
        <ul class="message message-error" role="alert">
            <?php foreach ($errors as $error): ?>
                <li><?= Helper::e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="<?= Helper::e(Helper::url('login')) ?>" class="stack-form">
        <label for="username">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            value="<?= Helper::e($username) ?>"
            autocomplete="username"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            required
        >

        <button type="submit">Log in</button>
    </form>
</section>
