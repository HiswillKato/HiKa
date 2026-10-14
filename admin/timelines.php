<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

guard();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check();
    $id = (int) post('id');

    if (post('doing') === 'delete' && $id > 0) {
        run('DELETE FROM timeline WHERE id = ?', [$id]);
        flash('Timeline entry deleted.');
    }
    redirect('admin/timelines');
}

$title   = 'Timeline';
$actions = '<a class="btn btn-primary btn-sm" href="' . panel('timeline') . '">' . icon('fa-solid fa-plus') . ' New entry</a>';
$items   = all('SELECT * FROM timeline ORDER BY sort, id');
$labels  = journeys();

require __DIR__ . '/parts/header.php';
?>

<?php if ($items === []): ?>
    <div class="card p-5 text-center">
        <p class="text-body-secondary mb-3">No entries yet.</p>
        <a class="btn btn-primary" href="<?= panel('timeline') ?>"><?= icon('fa-solid fa-plus') ?> Add an entry</a>
    </div>
<?php else: ?>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Entry</th>
                        <th>Period</th>
                        <th>Type</th>
                        <th>Shown</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <span class="fw-semibold d-block"><?= e($item['title']) ?></span>
                                <span class="small text-body-secondary"><?= e($item['place']) ?></span>
                            </td>
                            <td><?= e($item['period']) ?></td>
                            <td><?= e($labels[$item['type']] ?? $item['type']) ?></td>
                            <td>
                                <?php if ($item['shown']): ?>
                                    <span class="badge rounded-pill text-bg-success">Yes</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill text-bg-secondary">No</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a class="btn btn-ghost btn-sm rounded-circle" href="<?= panel('timeline?id=' . $item['id']) ?>"
                                       title="Edit"><?= icon('fa-solid fa-pen') ?></a>
                                    <form method="post" action="<?= panel('timelines') ?>" class="d-inline">
                                        <?= csrf() ?>
                                        <input type="hidden" name="doing" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                        <button class="btn btn-ghost btn-sm rounded-circle text-danger" type="submit"
                                                data-confirm="Delete &quot;<?= e($item['title']) ?>&quot;?"
                                                title="Delete"><?= icon('fa-solid fa-trash') ?></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/parts/footer.php'; ?>