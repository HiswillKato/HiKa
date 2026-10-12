<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

guard();

$id  = (int) ($_GET['id'] ?? 0);
$row = $id > 0 ? one('SELECT * FROM skills WHERE id = ?', [$id]) : null;

if ($id > 0 && $row === null) {
    flash('That skill no longer exists.', 'danger');
    redirect('admin/skills');
}

$data = [
    'name'     => $row['name']     ?? '',
    'category' => $row['category'] ?? 'language',
    'level'    => (int) ($row['level'] ?? 0),
    'icon'     => $row['icon']     ?? 'fa-solid fa-code',
    'note'     => $row['note']     ?? '',
    'sort'     => (int) ($row['sort'] ?? 0),
    'shown'    => (int) ($row['shown'] ?? 1),
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check();

    $data['name']     = post('name');
    $data['category'] = array_key_exists(post('category'), categories()) ? post('category') : 'language';
    $data['level']    = max(0, min(100, (int) post('level')));
    $data['icon']     = post('icon', 'fa-solid fa-code');
    $data['note']     = post('note');
    $data['sort']     = (int) post('sort');
    $data['shown']    = (int) isset($_POST['shown']);

    if ($data['name'] === '') {
        $errors[] = 'A name is required.';
    }

    if ($errors === []) {
        if ($row !== null) {
            run('UPDATE skills SET name=?, category=?, level=?, icon=?, note=?, sort=?, shown=? WHERE id=?',
                [$data['name'], $data['category'], $data['level'], $data['icon'], $data['note'], $data['sort'], $data['shown'], $id]);
        } else {
            add('INSERT INTO skills (name, category, level, icon, note, sort, shown) VALUES (?,?,?,?,?,?,?)',
                [$data['name'], $data['category'], $data['level'], $data['icon'], $data['note'], $data['sort'], $data['shown']]);
        }

        flash('Skill saved.');
        redirect('admin/skills');
    }
}

$title   = $row ? 'Edit skill' : 'New skill';
$actions = '<a class="btn btn-ghost btn-sm" href="' . panel('skills') . '">' . icon('fa-solid fa-arrow-left') . ' Back</a>';

require __DIR__ . '/parts/header.php';
?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card p-4">
            <?php if ($errors !== []): ?>
                <div class="alert alert-danger" role="alert"><?= e(implode(' ', $errors)) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= panel('skill' . ($id ? '?id=' . $id : '')) ?>">
                <?= csrf() ?>

                <label class="form-label fw-semibold" for="name">Name *</label>
                <input class="form-control mb-3" id="name" name="name" value="<?= e($data['name']) ?>" required autofocus>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="category">Group</label>
                        <select class="form-select" id="category" name="category">
                            <?= options(categories(), $data['category']) ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="level">Level (0–100)</label>
                        <input class="form-control" id="level" name="level" type="number" min="0" max="100" value="<?= (int) $data['level'] ?>">
                        <div class="form-text">0 shows a chip instead of a bar.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="icon">Font Awesome icon</label>
                        <input class="form-control" id="icon" name="icon" value="<?= e($data['icon']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="sort">Order</label>
                        <input class="form-control" id="sort" name="sort" type="number" value="<?= (int) $data['sort'] ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="note">Note</label>
                        <input class="form-control" id="note" name="note" value="<?= e($data['note']) ?>" maxlength="200">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="shown" name="shown" <?= $data['shown'] ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="shown">Show on the site</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary" type="submit"><?= icon('fa-solid fa-floppy-disk') ?> Save skill</button>
                    <a class="btn btn-ghost" href="<?= panel('skills') ?>">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card p-4">
            <span class="eyebrow mb-3">Preview</span>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold"><i class="<?= e($data['icon']) ?> text-primary me-2"></i><?= e($data['name'] ?: 'Skill name') ?></span>
                <span class="mono small text-body-secondary"><?= (int) $data['level'] ?>%</span>
            </div>
            <div class="meter"><span style="width: <?= (int) $data['level'] ?>%"></span></div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/parts/footer.php'; ?>
