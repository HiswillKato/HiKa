<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

guard();

$row = terminal();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check();

    $values = [
        'host'  => post('host'),
        'path'  => post('path'),
        'lines' => post('lines'),
    ];

    /* The terminal is a single row (id = 1), like the profile. */
    if ($row === []) {
        add('INSERT INTO terminal (id, host, path, `lines`) VALUES (1,?,?,?)', array_values($values));
    } else {
        run('UPDATE terminal SET host=?, path=?, `lines`=? WHERE id=?', [...array_values($values), $row['id']]);
    }

    flash('Terminal saved.');
    redirect('admin/terminal');
}

$title   = 'Terminal';
$actions = '<a class="btn btn-ghost btn-sm" href="' . url() . '#terminal" target="_blank" rel="noopener">' . icon('fa-solid fa-eye') . ' View</a>';

require __DIR__ . '/parts/header.php';
?>

<form method="post" action="<?= panel('terminal') ?>">
    <?= csrf() ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card p-4">
                <h2 class="h6 eyebrow mb-3">The prompt</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="host">User@host</label>
                        <input class="form-control" id="host" name="host" value="<?= e($row['host'] ?? 'hiswill@localhost') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="path">Folder</label>
                        <input class="form-control" id="path" name="path" value="<?= e($row['path'] ?? '~') ?>">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="form-label fw-semibold" for="lines">Lines the terminal prints</label>
                    <textarea class="form-control mono" id="lines" name="lines" rows="9"><?= e($row['lines'] ?? '') ?></textarea>
                    <div class="form-text">
                        One row per line of output — <code>c::whoami</code> prints a typed command,
                        <code>out::Hello there</code> prints plain output (<code>{name}</code> and <code>{title}</code>
                        become your profile), and <code>type::A, B, C</code> prints a rotating typing line.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card p-4 mb-4">
                <button class="btn btn-primary w-100" type="submit">
                    <?= icon('fa-solid fa-floppy-disk') ?> Save terminal
                </button>
            </div>

            <div class="card p-4">
                <h2 class="h6 eyebrow mb-3">Preview</h2>
                <div class="terminal">
                    <div class="terminal-bar">
                        <span class="terminal-dot red"></span>
                        <span class="terminal-dot amber"></span>
                        <span class="terminal-dot green"></span>
                        <span class="terminal-title"><?= e($row['host'] ?? 'hiswill@localhost') ?>: <?= e($row['path'] ?? '~') ?></span>
                    </div>
                    <div class="terminal-body">
                        <?= termHtml() ?>
                    </div>
                </div>
                <div class="form-text text-center mt-2">How the terminal looks after saving.</div>
            </div>
        </div>
    </div>
</form>

<?php require __DIR__ . '/parts/footer.php'; ?>