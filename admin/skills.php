<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

guard();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check();
    $id = (int) post('id');

    if (post('doing') === 'delete' && $id > 0) {
        run('DELETE FROM skills WHERE id = ?', [$id]);
        flash('Skill deleted.');
    }
    redirect('admin/skills');
}

$title   = 'Skills';
$actions = '<a class="btn btn-primary btn-sm" href="' . panel('skill') . '">' . icon('fa-solid fa-plus') . ' New skill</a>';
$items   = all('SELECT * FROM skills ORDER BY FIELD(category, "language","stack","interest","practice"), sort, name');
$labels  = categories();

require __DIR__ . '/parts/header.php';
?>

<?php if ($items === []): ?>
    <div class="card p-5 text-center">
        <p class="text-body-secondary mb-3">No skills yet.</p>
        <a class="btn btn-primary" href="<?= panel('skill') ?>"><?= icon('fa-solid fa-plus') ?> Add a skill</a>
    </div>
<?php else: ?>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Skill</th>
                        <th>Group</th>
                        <th class="d-none d-md-table-cell">Level</th>
                        <th>Shown</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <span class="fw-semibold">
                                    <i class="<?= e($item['icon']) ?> text-primary me-2"></i><?= e($item['name']) ?>
                                </span>
                            </td>
                            <td><?= e($labels[$item['category']] ?? $item['category']) ?></td>
                            <td class="d-none d-md-table-cell">
                                <?= $item['level'] ? (int) $item['level'] . '%' : '<span class="text-body-secondary">—</span>' ?>
                            </td>
                            <td>
                                <?php if ($item['shown']): ?>
                                    <span class="badge rounded-pill text-bg-success">Yes</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill text-bg-secondary">No</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a class="btn btn-ghost btn-sm rounded-circle" href="<?= panel('skill?id=' . $item['id']) ?>"
                                       title="Edit"><?= icon('fa-solid fa-pen') ?></a>
                                    <form method="post" action="<?= panel('skills') ?>" class="d-inline">
                                        <?= csrf() ?>
                                        <input type="hidden" name="doing" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                        <button class="btn btn-ghost btn-sm rounded-circle text-danger" type="submit"
                                                data-confirm="Delete &quot;<?= e($item['name']) ?>&quot;?"
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
