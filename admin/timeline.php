<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

guard();

$id  = (int) ($_GET['id'] ?? 0);
$row = $id > 0 ? one('SELECT * FROM timeline WHERE id = ?', [$id]) : null;

if ($id > 0 && $row === null) {
    flash('That timeline entry no longer exists.', 'danger');
    redirect('admin/timelines');
}

$data = [
    'title'  => $row['title']  ?? '',
    'place'  => $row['place']  ?? '',
    'period' => $row['period'] ?? '',
    'detail' => $row['detail'] ?? '',
    'type'   => $row['type']   ?? 'milestone',
    'sort'   => (int) ($row['sort'] ?? 0),
    'shown'  => (int) ($row['shown'] ?? 1),
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check();

    $data['title']  = post('title');
    $data['place']  = post('place');
    $data['period'] = post('period');
    $data['detail'] = post('detail');
    $data['type']   = array_key_exists(post('type'), journeys()) ? post('type') : 'milestone';
    $data['sort']   = (int) post('sort');
    $data['shown']  = (int) isset($_POST['shown']);

    if ($data['title'] === '') {
        $errors[] = 'A title is required.';
    }

    if ($errors === []) {
        if ($row !== null) {
            run('UPDATE timeline SET title=?, place=?, period=?, detail=?, type=?, sort=?, shown=? WHERE id=?',
                [$data['title'], $data['place'], $data['period'], $data['detail'], $data['type'], $data['sort'], $data['shown'], $id]);
        } else {
            add('INSERT INTO timeline (title, place, period, detail, type, sort, shown) VALUES (?,?,?,?,?,?,?)',
                [$data['title'], $data['place'], $data['period'], $data['detail'], $data['type'], $data['sort'], $data['shown']]);
        }

        flash('Timeline entry saved.');
        redirect('admin/timelines');
    }
}

$title   = $row ? 'Edit entry' : 'New entry';
$actions = '<a class="btn btn-ghost btn-sm" href="' . panel('timelines') . '">' . icon('fa-solid fa-arrow-left') . ' Back</a>';

require __DIR__ . '/parts/header.php';
?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card p-4">
            <?php if ($errors !== []): ?>
                <div class="alert alert-danger" role="alert"><?= e(implode(' ', $errors)) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= panel('timeline' . ($id ? '?id=' . $id : '')) ?>">
                <?= csrf() ?>

                <label class="form-label fw-semibold" for="title">Title *</label>
                <input class="form-control mb-3" id="title" name="title" value="<?= e($data['title']) ?>" required autofocus>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="place">Place</label>
                        <input class="form-control" id="place" name="place" value="<?= e($data['place']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="period">Period</label>
                        <input class="form-control" id="period" name="period" value="<?= e($data['period']) ?>" placeholder="2022 — 2026">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="type">Type</label>
                        <select class="form-select" id="type" name="type">
                            <?= options(journeys(), $data['type']) ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="sort">Order</label>
                        <input class="form-control" id="sort" name="sort" type="number" value="<?= (int) $data['sort'] ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="detail">Detail</label>
                        <textarea class="form-control" id="detail" name="detail" rows="3" maxlength="280"><?= e($data['detail']) ?></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="shown" name="shown" <?= $data['shown'] ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="shown">Show on the site</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary" type="submit"><?= icon('fa-solid fa-floppy-disk') ?> Save entry</button>
                    <a class="btn btn-ghost" href="<?= panel('timelines') ?>">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card p-4">
            <span class="eyebrow mb-3">Preview</span>
            <div class="timeline-item">
                <div class="timeline-period mb-1">
                    <?= e($data['period'] ?: 'Period') ?>
                    &middot; <?= e(ucfirst($data['type'])) ?>
                </div>
                <h3 class="h5 mb-1"><?= e($data['title'] ?: 'Entry title') ?></h3>
                <?php if ($data['place'] !== ''): ?>
                    <div class="fw-semibold text-body-secondary mb-1"><?= e($data['place']) ?></div>
                <?php endif; ?>
                <?php if ($data['detail'] !== ''): ?>
                    <p class="text-body-secondary mb-0"><?= e($data['detail']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/parts/footer.php'; ?>