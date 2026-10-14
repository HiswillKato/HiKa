<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

$title = 'Settings';
guard();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check();

    /* Every switch in settings() is posted, so the flags table is rebuilt in
       one INSERT — all switches stored, no half-saved rows. */
    $pairs = [];
    $vals  = [];

    foreach (settings() as $key => $meta) {
        $pairs[] = '(?, ?)';
        $vals[]  = $key;
        $vals[]  = (int) isset($_POST['flags'][$key]);
    }

    run('DELETE FROM flags');
    run('INSERT INTO flags (`key`, value) VALUES ' . implode(', ', $pairs), $vals);

    flash('Settings saved.');
    redirect('admin/settings');
}

require __DIR__ . '/parts/header.php';
?>

<form method="post" action="<?= panel('settings') ?>">
    <?= csrf() ?>

    <div class="card" style="max-width: 44rem;">
        <div class="card-body">
            <?php foreach (settings() as $key => [$label, $help]): ?>
                <div class="form-check form-switch form-check-reverse d-flex justify-content-between align-items-center gap-3 mb-3">
                    <div class="min-width-0">
                        <label class="form-check-label fw-semibold" for="flag-<?= e($key) ?>"><?= e($label) ?></label>
                        <div class="form-text mb-0"><?= e($help) ?></div>
                    </div>
                    <input class="form-check-input flex-shrink-0" type="checkbox" role="switch"
                           id="flag-<?= e($key) ?>" name="flags[<?= e($key) ?>]"
                           <?= flag($key) ? 'checked' : '' ?>>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary" type="submit"><?= icon('fa-solid fa-floppy-disk') ?> Save settings</button>
        </div>
    </div>
</form>

<?php require __DIR__ . '/parts/footer.php'; ?>