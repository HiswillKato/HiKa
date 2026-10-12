<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

guard();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check();
    $id = (int) post('id');

    if (post('doing') === 'delete' && $id > 0) {
        run('DELETE FROM messages WHERE id = ?', [$id]);
        flash('Message deleted.');
    }
    redirect('admin/messages');
}

$title = 'Messages';
$items = all('SELECT id, name, email, subject, seen, created FROM messages ORDER BY created DESC');

require __DIR__ . '/parts/header.php';
?>

<?php if ($items === []): ?>
    <div class="card p-5 text-center">
        <?= icon('fa-regular fa-envelope fa-2x text-primary mb-3') ?>
        <p class="text-body-secondary mb-0">No messages yet. When somebody uses the contact form, it lands here.</p>
    </div>
<?php else: ?>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>From</th>
                        <th class="d-none d-md-table-cell">Subject</th>
                        <th>Received</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <a class="d-flex align-items-center gap-2 text-decoration-none" href="<?= panel('message?id=' . $item['id']) ?>">
                                    <span class="brand-mark" style="width:2.25rem;height:2.25rem;font-size:.8rem;">
                                        <?= e(initials($item['name'])) ?>
                                    </span>
                                    <span>
                                        <span class="fw-semibold d-block text-body">
                                            <?= e($item['name']) ?>
                                            <?php if (!$item['seen']): ?><span class="badge rounded-pill text-bg-warning ms-1">New</span><?php endif; ?>
                                        </span>
                                        <span class="small text-body-secondary"><?= e($item['email']) ?></span>
                                    </span>
                                </a>
                            </td>
                            <td class="d-none d-md-table-cell text-truncate" style="max-width:16rem;">
                                <?= e($item['subject'] !== '' ? $item['subject'] : '—') ?>
                            </td>
                            <td class="text-nowrap small text-body-secondary"><?= e(ago($item['created'])) ?></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a class="btn btn-ghost btn-sm rounded-circle" href="<?= panel('message?id=' . $item['id']) ?>" title="Open"><?= icon('fa-solid fa-eye') ?></a>
                                    <form method="post" action="<?= panel('messages') ?>" class="d-inline">
                                        <?= csrf() ?>
                                        <input type="hidden" name="doing" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                        <button class="btn btn-ghost btn-sm rounded-circle text-danger" type="submit"
                                                data-confirm="Delete the message from <?= e($item['name']) ?>?"
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
