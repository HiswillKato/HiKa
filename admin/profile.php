<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

$user = guard();
$row  = me();

/* ---- changing the password lives on this page too, as its own action ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('doing') === 'password') {
    check();

    $current = (string) ($_POST['current'] ?? '');
    $fresh   = (string) ($_POST['fresh'] ?? '');
    $again   = (string) ($_POST['again'] ?? '');
    $account = one('SELECT password FROM users WHERE id = ?', [$user['id']]);

    if (!$account || !password_verify($current, $account['password'])) {
        flash('The current password was not right.', 'danger');
    } elseif (mb_strlen($fresh) < MIN_PASSWORD) {
        flash('A new password needs at least ' . MIN_PASSWORD . ' characters.', 'danger');
    } elseif ($fresh !== $again) {
        flash('The two new passwords did not match.', 'danger');
    } else {
        run('UPDATE users SET password = ? WHERE id = ?', [password_hash($fresh, PASSWORD_DEFAULT), $user['id']]);
        flash('Password changed.');
    }

    redirect('admin/profile');
}

/* ---- saving the profile ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check();

    $values = [
        'name'      => post('name'),
        'alias'     => post('alias'),
        'title'     => post('title'),
        'tagline'   => post('tagline'),
        'bio'       => post('bio'),
        'about'     => post('about'),
        'location'  => post('location'),
        'campus'    => post('campus'),
        'degree'    => post('degree'),
        'email'     => post('email'),
        'phone'     => post('phone'),
        'resume'    => post('resume'),
        'available' => (int) isset($_POST['available']),
    ];

    if ($values['name'] === '' || $values['title'] === '') {
        flash('A name and a title are required.', 'danger');
        redirect('admin/profile');
    }

    /* Avatar: replace, or clear when the remove box is ticked. */
    $avatar = $row['avatar'] ?? '';
    if (!empty($_FILES['avatar']['name'])) {
        $stored = store($_FILES['avatar'], 'avatar');
        if ($stored !== '') {
            remove($avatar);
            $avatar = $stored;
        } else {
            flash('That picture was not a JPG, PNG or WebP under 3 MB — the old one is still in place.', 'danger');
        }
    } elseif (isset($_POST['clear_avatar']) && $avatar !== '') {
        remove($avatar);
        $avatar = '';
    }

    if ($row === []) {
        add(
            'INSERT INTO profile (name, alias, title, tagline, bio, about, location, campus, degree, email, phone, resume, available, avatar) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [...array_values($values), $avatar]
        );
    } else {
        run(
            'UPDATE profile SET name=?, alias=?, title=?, tagline=?, bio=?, about=?, location=?, campus=?, degree=?, email=?, phone=?, resume=?, available=?, avatar=? WHERE id=?',
            [...array_values($values), $avatar, $row['id']]
        );
    }

    flash('Profile saved.');
    redirect('admin/profile');
}

$title   = 'Profile';
$actions = '<a class="btn btn-ghost btn-sm" href="' . url('about') . '" target="_blank" rel="noopener">' . icon('fa-solid fa-eye') . ' View</a>';

require __DIR__ . '/parts/header.php';
?>

<form method="post" action="<?= panel('profile') ?>" enctype="multipart/form-data">
    <?= csrf() ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card p-4 mb-4">
                <h2 class="h6 eyebrow mb-3">Identity</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="name">Name *</label>
                        <input class="form-control" id="name" name="name" value="<?= e($row['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="alias">Alias / nickname</label>
                        <input class="form-control" id="alias" name="alias" value="<?= e($row['alias'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="title">Title *</label>
                        <input class="form-control" id="title" name="title" value="<?= e($row['title'] ?? '') ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="tagline">Tagline</label>
                        <input class="form-control" id="tagline" name="tagline" maxlength="200" value="<?= e($row['tagline'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="bio">Short bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3"><?= e($row['bio'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="about">About (the long story)</label>
                        <textarea class="form-control" id="about" name="about" rows="12"><?= e($row['about'] ?? '') ?></textarea>
                        <div class="form-text">Blank line = new paragraph, <code>## Heading</code> = heading, lines starting with <code>-</code> = a list.</div>
                    </div>
                </div>
            </div>

            <div class="card p-4">
                <h2 class="h6 eyebrow mb-3">Details</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="location">Location</label>
                        <input class="form-control" id="location" name="location" value="<?= e($row['location'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="campus">Campus / company</label>
                        <input class="form-control" id="campus" name="campus" value="<?= e($row['campus'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="degree">Course / degree</label>
                        <input class="form-control" id="degree" name="degree" value="<?= e($row['degree'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="email">Email</label>
                        <input class="form-control" id="email" name="email" type="email" value="<?= e($row['email'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="phone">Phone</label>
                        <input class="form-control" id="phone" name="phone" value="<?= e($row['phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="resume">Résumé link</label>
                        <input class="form-control" id="resume" name="resume" value="<?= e($row['resume'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="available" name="available" <?= !empty($row['available']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="available">Show “available for work” badge</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card p-4 mb-4">
                <h2 class="h6 eyebrow mb-3">Photo</h2>
                <div class="text-center mb-3">
                    <?php if (!empty($row['avatar'])): ?>
                        <img src="<?= upload($row['avatar']) ?>" alt="Current photo" class="rounded-circle"
                             style="width: 7rem; height: 7rem; object-fit: cover;">
                    <?php else: ?>
                        <span class="brand-mark mx-auto d-flex" style="width:5rem;height:5rem;font-size:1.6rem;">
                            <?= e(initials($row['name'] ?? OWNER)) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <label class="form-label fw-semibold" for="avatar">Replace picture</label>
                <input class="form-control mb-2" id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp">
                <div class="form-text mb-2">JPG, PNG or WebP, up to 3 MB. A square photo looks best.</div>
                <?php if (!empty($row['avatar'])): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="clear_avatar" name="clear_avatar">
                        <label class="form-check-label" for="clear_avatar">Remove the current picture</label>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card p-4">
                <button class="btn btn-primary w-100" type="submit">
                    <?= icon('fa-solid fa-floppy-disk') ?> Save profile
                </button>
            </div>
        </div>
    </div>
</form>

<div class="row g-4 mt-1">
    <div class="col-lg-8">
        <div class="card p-4">
            <h2 class="h6 eyebrow mb-3">Change password</h2>
            <form method="post" action="<?= panel('profile') ?>">
                <?= csrf() ?>
                <input type="hidden" name="doing" value="password">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="current">Current</label>
                        <input class="form-control" id="current" name="current" type="password" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="fresh">New</label>
                        <input class="form-control" id="fresh" name="fresh" type="password" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="again">Repeat new</label>
                        <input class="form-control" id="again" name="again" type="password" required>
                    </div>
                </div>
                <button class="btn btn-ghost mt-3" type="submit"><?= icon('fa-solid fa-key') ?> Change password</button>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/parts/footer.php'; ?>
