<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

$user = guard();

$id  = (int) ($_GET['id'] ?? 0);
$row = $id > 0 ? one('SELECT * FROM projects WHERE id = ?', [$id]) : null;

if ($id > 0 && $row === null) {
    flash('That project no longer exists.', 'danger');
    redirect('admin/projects');
}

$data = [
    'title'    => $row['title']    ?? '',
    'summary'  => $row['summary']  ?? '',
    'body'     => $row['body']     ?? '',
    'tech'     => $row['tech']     ?? '',
    'category' => $row['category'] ?? 'web',
    'repo'     => $row['repo']     ?? '',
    'live'     => $row['live']     ?? '',
    'icon'     => $row['icon']     ?? 'fa-solid fa-code',
    'featured' => (int) ($row['featured'] ?? 0),
    'status'   => $row['status']   ?? 'published',
    'sort'     => (int) ($row['sort'] ?? 0),
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check();

    foreach ($data as $key => $ignored) {
        $data[$key] = $key === 'featured' ? (int) isset($_POST['featured']) : post($key, (string) $data[$key]);
    }
    $data['sort']  = (int) post('sort', '0');
    $data['status'] = post('status') === 'draft' ? 'draft' : 'published';

    if (trim((string) $data['title']) === '') {
        $errors[] = 'A title is required.';
    }

    if ($errors === []) {
        if ($row !== null) {
            run(
                'UPDATE projects SET title=?, summary=?, body=?, tech=?, category=?, repo=?, live=?, icon=?, featured=?, status=?, sort=? WHERE id=?',
                [$data['title'], $data['summary'], $data['body'], $data['tech'], $data['category'],
                 $data['repo'], $data['live'], $data['icon'], $data['featured'], $data['status'], $data['sort'], $id]
            );
        } else {
            $base = slug((string) $data['title']) ?: 'project';
            $made = $base;
            $tries = 2;
            while (total('SELECT COUNT(*) FROM projects WHERE slug = ?', [$made]) > 0) {
                $made = $base . '-' . $tries++;
            }

            add(
                'INSERT INTO projects (title, slug, summary, body, tech, category, repo, live, icon, featured, status, sort) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)',
                [$data['title'], $made, $data['summary'], $data['body'], $data['tech'], $data['category'],
                 $data['repo'], $data['live'], $data['icon'], $data['featured'], $data['status'], $data['sort']]
            );
        }

        flash('Project saved.');
        redirect('admin/projects');
    }
}

$title   = $row ? 'Edit project' : 'New project';
$actions = '<a class="btn btn-ghost btn-sm" href="' . panel('projects') . '">' . icon('fa-solid fa-arrow-left') . ' Back</a>';

require __DIR__ . '/parts/header.php';
?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4">
            <?php if ($errors !== []): ?>
                <div class="alert alert-danger" role="alert"><?= e(implode(' ', $errors)) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= panel('project' . ($id ? '?id=' . $id : '')) ?>">
                <?= csrf() ?>

                <label class="form-label fw-semibold" for="title">Title *</label>
                <input class="form-control mb-3" id="title" name="title"
                       value="<?= e((string) $data['title']) ?>"
                       <?= $row ? '' : 'data-slug-source' ?> required autofocus>

                <label class="form-label fw-semibold" for="summary">Summary</label>
                <textarea class="form-control mb-1" id="summary" name="summary" rows="2" maxlength="280"><?= e((string) $data['summary']) ?></textarea>
                <div class="form-text mb-3">One or two sentences shown on the cards. Up to 280 characters.</div>

                <label class="form-label fw-semibold" for="body">Full description</label>
                <textarea class="form-control mb-1" id="body" name="body" rows="12"><?= e((string) $data['body']) ?></textarea>
                <div class="form-text mb-4">
                    Blank line = new paragraph, <code>## Heading</code> = heading,
                    lines starting with <code>-</code> = a list.
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="tech">Tech (comma separated)</label>
                        <input class="form-control" id="tech" name="tech" value="<?= e((string) $data['tech']) ?>" placeholder="PHP, MySQLi, Bootstrap 5">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="category">Category</label>
                        <input class="form-control" id="category" name="category" value="<?= e((string) $data['category']) ?>" list="cats">
                        <datalist id="cats">
                            <option value="web"></option>
                            <option value="tool"></option>
                            <option value="mobile"></option>
                            <option value="other"></option>
                        </datalist>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="repo">Repository URL</label>
                        <input class="form-control" id="repo" name="repo" type="url" value="<?= e((string) $data['repo']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="live">Live URL</label>
                        <input class="form-control" id="live" name="live" type="url" value="<?= e((string) $data['live']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="icon">Font Awesome icon</label>
                        <input class="form-control" id="icon" name="icon" value="<?= e((string) $data['icon']) ?>">
                        <div class="form-text">e.g. <code>fa-solid fa-church</code></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="sort">Order</label>
                        <input class="form-control" id="sort" name="sort" type="number" value="<?= (int) $data['sort'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="status">Status</label>
                        <select class="form-select" id="status" name="status">
                            <?= options(['published' => 'Published', 'draft' => 'Draft'], (string) $data['status']) ?>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="featured" name="featured"
                                   <?= $data['featured'] ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="featured">Show on the home page</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary" type="submit"><?= icon('fa-solid fa-floppy-disk') ?> Save project</button>
                    <a class="btn btn-ghost" href="<?= panel('projects') ?>">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card p-4 mb-4">
            <span class="eyebrow mb-2">Address</span>
            <?php if ($row !== null): ?>
                <p class="text-body-secondary small mb-2">Saved once and kept, so old links never break.</p>
                <code><?= e(url('projects/' . $row['slug'])) ?></code>
            <?php else: ?>
                <p class="text-body-secondary small mb-2">Made from the title when you save.</p>
                <code><?= e(url('projects/')) ?><span data-slug-preview data-slug-new>your-project</span></code>
            <?php endif; ?>
        </div>

        <div class="card p-4">
            <span class="eyebrow mb-3">Preview</span>
            <div class="d-flex align-items-center gap-2">
                <span class="work-icon"><?= icon((string) $data['icon']) ?></span>
                <span class="fw-semibold"><?= e((string) $data['title'] ?: 'Project title') ?></span>
            </div>
        </div>

        <?php if ($row !== null): ?>
            <div class="card p-4 mt-4 border-danger">
                <span class="eyebrow mb-2" style="color: var(--bs-danger)">Danger zone</span>
                <form method="post" action="<?= panel('projects') ?>">
                    <?= csrf() ?>
                    <input type="hidden" name="doing" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $id ?>">
                    <button class="btn btn-ghost w-100 text-danger" type="submit"
                            data-confirm="Delete this project for good?">
                        <?= icon('fa-solid fa-trash') ?> Delete project
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/parts/footer.php'; ?>
