<?php

declare(strict_types=1);

use App\Core\Helper;
use App\Entity\Expense;

/** @var list<Expense> $expenses */
/** @var float $total */
/** @var string $flash */
?>
<section class="content-panel" aria-labelledby="list-heading">
    <div class="panel-heading">
        <div>
            <h1 id="list-heading">Saved expenses</h1>
            <p class="panel-note">Expenses are stored in MySQL via Doctrine ORM.</p>
        </div>
        <a class="button-link" href="<?= Helper::e(Helper::url('expenses/create')) ?>">Add expense</a>
    </div>

    <?php require __DIR__ . '/../partials/flash.php'; ?>

    <?php if ($expenses === []): ?>
        <p class="empty-state">No expenses saved yet. Add one to get started.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Item</th>
                        <th class="num">Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><?= Helper::e($expense->getDate()) ?></td>
                            <td><?= Helper::e($expense->getCategory()) ?></td>
                            <td><?= Helper::e($expense->getItem()) ?></td>
                            <td class="num"><?= Helper::e($expense->formatAmount()) ?></td>
                            <td class="actions">
                                <a class="text-link" href="<?= Helper::e(Helper::url('expenses/edit') . '&id=' . (int) $expense->getId()) ?>">Edit</a>
                                <form method="post" action="<?= Helper::e(Helper::url('expenses/delete')) ?>" class="inline-form" onsubmit="return confirm('Delete this expense?');">
                                    <input type="hidden" name="id" value="<?= (int) $expense->getId() ?>">
                                    <button type="submit" class="link-button">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">Total expenditure</th>
                        <td class="num"><?= Helper::e(number_format($total, 2)) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>
</section>
