<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

guard();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check();
    $id = (int) post('id');

    if (post('doing') === 'delete' && $id > 0) {
        run('DELETE FROM socials WHERE id = ?', [$id]);
        flash('Social link deleted.');
    }
    redirect('admin/socials');
}

$title   = 'Socials';
$actions = '<a class="btn btn-primary btn-sm" href="' . panel('social') . '">' . icon('fa-solid fa-plus') . ' New link</a>';
$items   = all('SELECT * FROM socials ORDER BY sort, id');

require __DIR__ . '/parts/header.php';
?>

<?php if ($items === []): ?>
    <div class="card p-5 text-center">
        <p class="text-body-secondary mb-3">No social links yet.</p>
        <a class="btn btn-primary" href="<?= panel('social') ?>"><?= icon('fa-solid fa-plus') ?> Add a link</a>
    </div>
<?php else: ?>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Network</th>
                        <th class="d-none d-md-table-cell">Handle</th>
                        <th>Shown</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <span class="d-inline-flex align-items-center gap-2">
                                    <span class="brand-mark" style="width:2.25rem;height:2.25rem;background-image:none;background-color:<?= e($item['color'] ?: 'var(--bs-primary)') ?>;color:#08131c;font-size:.9rem;">
                                        <?= icon($item['icon']) ?>
                                    </span>
                                    <span class="fw-semibold"><?= e($item['name']) ?></span>
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell mono small"><?= e($item['handle']) ?></td>
                            <td>
                                <?php if ($item['shown']): ?>
                                    <span class="badge rounded-pill text-bg-success">Yes</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill text-bg-secondary">No</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a class="btn btn-ghost btn-sm rounded-circle" href="<?= panel('social?id=' . $item['id']) ?>"
                                       title="Edit"><?= icon('fa-solid fa-pen') ?></a>
                                    <form method="post" action="<?= panel('socials') ?>" class="d-inline">
                                        <?= csrf() ?>
                                        <input type="hidden" name="doing" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                        <button class="btn btn-ghost btn-sm rounded-circle text-danger" type="submit"
                                                data-confirm="Delete the <?= e($item['name']) ?> link?"
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
