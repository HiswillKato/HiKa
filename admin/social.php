<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

guard();

$id  = (int) ($_GET['id'] ?? 0);
$row = $id > 0 ? one('SELECT * FROM socials WHERE id = ?', [$id]) : null;

if ($id > 0 && $row === null) {
    flash('That link no longer exists.', 'danger');
    redirect('admin/socials');
}

$data = [
    'name'   => $row['name']   ?? '',
    'handle' => $row['handle'] ?? '',
    'url'    => $row['url']    ?? '',
    'icon'   => $row['icon']   ?? 'fa-brands fa-github',
    'color'  => $row['color']  ?? '#2ee6a8',
    'sort'   => (int) ($row['sort'] ?? 0),
    'shown'  => (int) ($row['shown'] ?? 1),
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check();

    $data['name']   = post('name');
    $data['handle'] = post('handle');
    $data['url']    = post('url');
    $data['icon']   = post('icon', 'fa-brands fa-github');
    $data['color']  = post('color', '#2ee6a8');
    $data['sort']   = (int) post('sort');
    $data['shown']  = (int) isset($_POST['shown']);

    if ($data['name'] === '') {
        $errors[] = 'A name is required.';
    }
    if ($data['url'] !== '' && !filter_var($data['url'], FILTER_VALIDATE_URL)) {
        $errors[] = 'The URL does not look right (leave it empty to show the handle as a copy button).';
    }

    if ($errors === []) {
        if ($row !== null) {
            run('UPDATE socials SET name=?, handle=?, url=?, icon=?, color=?, sort=?, shown=? WHERE id=?',
                [$data['name'], $data['handle'], $data['url'], $data['icon'], $data['color'], $data['sort'], $data['shown'], $id]);
        } else {
            add('INSERT INTO socials (name, handle, url, icon, color, sort, shown) VALUES (?,?,?,?,?,?,?)',
                [$data['name'], $data['handle'], $data['url'], $data['icon'], $data['color'], $data['sort'], $data['shown']]);
        }

        flash('Social link saved.');
        redirect('admin/socials');
    }
}

$title   = $row ? 'Edit social link' : 'New social link';
$actions = '<a class="btn btn-ghost btn-sm" href="' . panel('socials') . '">' . icon('fa-solid fa-arrow-left') . ' Back</a>';

require __DIR__ . '/parts/header.php';
?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card p-4">
            <?php if ($errors !== []): ?>
                <div class="alert alert-danger" role="alert"><?= e(implode(' ', $errors)) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= panel('social' . ($id ? '?id=' . $id : '')) ?>">
                <?= csrf() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="name">Network *</label>
                        <input class="form-control" id="name" name="name" value="<?= e($data['name']) ?>" required autofocus>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="handle">Handle</label>
                        <input class="form-control" id="handle" name="handle" value="<?= e($data['handle']) ?>" placeholder="@HiswillKato">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="url">URL</label>
                        <input class="form-control" id="url" name="url" type="url" value="<?= e($data['url']) ?>">
                        <div class="form-text">Leave empty to show the handle as a copy button.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="icon">Icon</label>
                        <select class="form-select" id="icon" name="icon">
                            <?php
                            $icons = platforms();
                            if (!in_array($data['icon'], $icons, true)) {
                                $icons['Custom — ' . $data['icon']] = $data['icon'];
                            }
                            echo options($icons, $data['icon']);
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="color">Brand colour</label>
                        <input class="form-control form-control-color" id="color" name="color" type="color" value="<?= e($data['color']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="sort">Order</label>
                        <input class="form-control" id="sort" name="sort" type="number" value="<?= (int) $data['sort'] ?>">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="shown" name="shown" <?= $data['shown'] ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="shown">Show on the site</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary" type="submit"><?= icon('fa-solid fa-floppy-disk') ?> Save link</button>
                    <a class="btn btn-ghost" href="<?= panel('socials') ?>">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card p-4">
            <span class="eyebrow mb-3">Preview</span>
            <span class="social" style="--brand: <?= e($data['color']) ?>"><?= icon($data['icon']) ?></span>
            <p class="text-body-secondary small mb-0 mt-3"><?= e($data['name'] ?: 'Network') ?> — <?= e($data['handle']) ?></p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/parts/footer.php'; ?>
