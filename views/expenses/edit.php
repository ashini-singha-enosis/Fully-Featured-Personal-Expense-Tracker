<?php

declare(strict_types=1);

use App\Core\Helper;

/** @var list<string> $categories */
/** @var list<string> $errors */
/** @var array{date: string, category: string, item: string, amount: string} $form */
/** @var int $expenseId */
?>
<section class="content-panel form-panel" aria-labelledby="edit-heading">
    <h1 id="edit-heading">Edit expense</h1>
    <p class="panel-note">Update the fields and save changes.</p>

    <?php if ($errors !== []): ?>
        <ul class="message message-error" role="alert">
            <?php foreach ($errors as $error): ?>
                <li><?= Helper::e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="<?= Helper::e(Helper::url('expenses/edit') . '&id=' . $expenseId) ?>" class="stack-form">
        <label for="date">Date</label>
        <input type="date" id="date" name="date" value="<?= Helper::e($form['date']) ?>" required>

        <label for="category">Category</label>
        <select id="category" name="category" required>
            <?php foreach ($categories as $category): ?>
                <option
                    value="<?= Helper::e($category) ?>"
                    <?= $form['category'] === $category ? 'selected' : '' ?>
                ><?= Helper::e($category) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="item">Item name</label>
        <input type="text" id="item" name="item" value="<?= Helper::e($form['item']) ?>" maxlength="100" required>

        <label for="amount">Amount spent</label>
        <input
            type="number"
            id="amount"
            name="amount"
            value="<?= Helper::e($form['amount']) ?>"
            min="0.01"
            step="0.01"
            inputmode="decimal"
            required
        >

        <div class="form-actions">
            <button type="submit">Update expense</button>
            <a class="text-link" href="<?= Helper::e(Helper::url('expenses')) ?>">Back to list</a>
        </div>
    </form>
</section>
