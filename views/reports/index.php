<?php

declare(strict_types=1);

use App\Core\Helper;

/** @var list<array{month: string, label: string, total: float, count: int}> $monthly */
/** @var list<array{category: string, total: float, count: int, percent: float}> $byCategory */
/** @var float $grandTotal */
/** @var string $flash */
?>
<section class="content-panel" aria-labelledby="reports-heading">
    <div class="panel-heading">
        <div>
            <h1 id="reports-heading">Spending reports</h1>
            <p class="panel-note">Monthly totals and category-wise breakdown from the database.</p>
        </div>
        <form method="post" action="<?= Helper::e(Helper::url('email/monthly-summary')) ?>">
            <button type="submit" class="button-link">Email monthly summary</button>
        </form>
    </div>

    <?php require __DIR__ . '/../partials/flash.php'; ?>

    <p class="report-total">Overall total: <strong><?= Helper::e(number_format($grandTotal, 2)) ?></strong></p>

    <div class="report-grid">
        <section aria-labelledby="monthly-heading">
            <h2 id="monthly-heading">Monthly expenses</h2>
            <?php if ($monthly === []): ?>
                <p class="empty-state">No monthly data yet.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th class="num">Entries</th>
                                <th class="num">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($monthly as $row): ?>
                                <tr>
                                    <td><?= Helper::e($row['label']) ?></td>
                                    <td class="num"><?= (int) $row['count'] ?></td>
                                    <td class="num"><?= Helper::e(number_format($row['total'], 2)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <section aria-labelledby="category-heading">
            <h2 id="category-heading">Category-wise spending</h2>
            <?php if ($byCategory === []): ?>
                <p class="empty-state">No category data yet.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="num">Entries</th>
                                <th class="num">Total</th>
                                <th class="num">Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($byCategory as $row): ?>
                                <tr>
                                    <td><?= Helper::e($row['category']) ?></td>
                                    <td class="num"><?= (int) $row['count'] ?></td>
                                    <td class="num"><?= Helper::e(number_format($row['total'], 2)) ?></td>
                                    <td class="num"><?= Helper::e(number_format($row['percent'], 1)) ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>
</section>
