<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

$user = guard();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check();
    $id = (int) post('id');

    if (post('doing') === 'delete' && $id > 0) {
        run('DELETE FROM projects WHERE id = ?', [$id]);
        flash('Project deleted.');
    }
    redirect('admin/projects');
}

$title   = 'Projects';
$actions = '<a class="btn btn-primary btn-sm" href="' . panel('project') . '">' . icon('fa-solid fa-plus') . ' New project</a>';
$items   = projects(true);

require __DIR__ . '/parts/header.php';
?>

<?php if ($items === []): ?>
    <div class="card p-5 text-center">
        <?= icon('fa-regular fa-folder-open fa-2x text-primary mb-3') ?>
        <p class="text-body-secondary mb-3">No projects yet.</p>
        <a class="btn btn-primary" href="<?= panel('project') ?>"><?= icon('fa-solid fa-plus') ?> Add your first project</a>
    </div>
<?php else: ?>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th class="d-none d-md-table-cell">Category</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="work-icon" style="width:2.25rem;height:2.25rem;font-size:1rem;">
                                        <?= icon($item['icon']) ?>
                                    </span>
                                    <span>
                                        <span class="fw-semibold d-block"><?= e($item['title']) ?></span>
                                        <span class="small text-body-secondary">/<?= e($item['slug']) ?></span>
                                    </span>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell text-capitalize"><?= e($item['category']) ?></td>
                            <td>
                                <?php if ($item['featured']): ?>
                                    <span class="badge rounded-pill text-bg-warning">Yes</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill text-bg-secondary">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($item['status'] === 'published'): ?>
                                    <span class="badge rounded-pill text-bg-success">Published</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill text-bg-secondary">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a class="btn btn-ghost btn-sm rounded-circle" href="<?= url('projects/' . $item['slug']) ?>"
                                       target="_blank" rel="noopener" title="View"><?= icon('fa-solid fa-eye') ?></a>
                                    <a class="btn btn-ghost btn-sm rounded-circle" href="<?= panel('project?id=' . $item['id']) ?>"
                                       title="Edit"><?= icon('fa-solid fa-pen') ?></a>
                                    <form method="post" action="<?= panel('projects') ?>" class="d-inline">
                                        <?= csrf() ?>
                                        <input type="hidden" name="doing" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                        <button class="btn btn-ghost btn-sm rounded-circle text-danger" type="submit"
                                                data-confirm="Delete &quot;<?= e($item['title']) ?>&quot;? This cannot be undone."
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
