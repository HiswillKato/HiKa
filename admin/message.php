<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

guard();

$id  = (int) ($_GET['id'] ?? 0);
$row = $id > 0 ? one('SELECT * FROM messages WHERE id = ?', [$id]) : null;

if ($row === null) {
    flash('That message no longer exists.', 'danger');
    redirect('admin/messages');
}

/* Opening a message marks it read, so the badge clears on the way out. */
if (!$row['seen']) {
    run('UPDATE messages SET seen = 1 WHERE id = ?', [$id]);
    $row['seen'] = 1;
}

$title   = 'Message';
$actions = '<a class="btn btn-ghost btn-sm" href="' . panel('messages') . '">' . icon('fa-solid fa-arrow-left') . ' All messages</a>';

require __DIR__ . '/parts/header.php';
?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span class="fw-semibold"><?= e($row['subject'] !== '' ? $row['subject'] : '(no subject)') ?></span>
                <span class="small text-body-secondary"><?= e(stamp($row['created'], 'j M Y, H:i')) ?></span>
            </div>
            <div class="card-body">
                <p style="white-space: pre-wrap; font-size: 1.05rem;"><?= e($row['body']) ?></p>
            </div>
            <div class="card-footer d-flex flex-wrap gap-2">
                <a class="btn btn-primary btn-sm" href="mailto:<?= e($row['email']) ?>?subject=Re: <?= e(rawurlencode($row['subject'] !== '' ? $row['subject'] : 'your message')) ?>">
                    <?= icon('fa-solid fa-reply') ?> Reply by email
                </a>
                <form method="post" action="<?= panel('messages') ?>" class="ms-auto">
                    <?= csrf() ?>
                    <input type="hidden" name="doing" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                    <button class="btn btn-ghost btn-sm text-danger" type="submit"
                            data-confirm="Delete this message for good?">
                        <?= icon('fa-solid fa-trash') ?> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="brand-mark" style="width:3rem;height:3rem;"><?= e(initials($row['name'])) ?></span>
                <div>
                    <div class="fw-semibold"><?= e($row['name']) ?></div>
                    <a class="small" href="mailto:<?= e($row['email']) ?>"><?= e($row['email']) ?></a>
                </div>
            </div>
            <ul class="list-unstyled d-grid gap-2 mb-0 small">
                <li><span class="text-body-secondary">Received:</span> <?= e(stamp($row['created'], 'j M Y, H:i')) ?></li>
                <?php if ($row['ip'] !== ''): ?>
                    <li><span class="text-body-secondary">IP:</span> <span class="mono"><?= e($row['ip']) ?></span></li>
                <?php endif; ?>
                <?php if ($row['agent'] !== ''): ?>
                    <li class="text-body-secondary"><?= e(cut($row['agent'], 90)) ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php require __DIR__ . '/parts/footer.php'; ?>
