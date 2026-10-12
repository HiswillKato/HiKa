<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

if (owner()) {
    redirect('admin/');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check();

    $email    = mb_substr(post('email'), 0, 160);
    $password = (string) ($_POST['password'] ?? '');

    if (attempt($email, $password)) {
        flash('Welcome back, ' . (owner()['name'] ?? '') . '.');
        redirect('admin/');
    }

    $error = 'That email and password did not match an account.';
}
?>
<!doctype html>
<html lang="en" data-theme="dark" data-bs-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign in · <?= e(OWNER) ?></title>
<meta name="robots" content="noindex, nofollow">
<link rel="icon" href="<?= asset('images/favicon.svg') ?>" type="image/svg+xml">
<link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= asset('css/all.min.css') ?>">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
<script>
(function () {
    try {
        var chosen = localStorage.getItem('hika-theme') || 'dark';
        document.documentElement.setAttribute('data-theme', chosen);
        document.documentElement.setAttribute('data-bs-theme', chosen);
    } catch (error) {}
})();
</script>
</head>
<body>
<div class="login-wrap">
    <div class="card p-4 p-sm-5 shadow-lg" style="width: 100%; max-width: 26rem;">
        <div class="text-center mb-4">
            <span class="brand-mark mx-auto d-flex mb-3" style="width: 3.25rem; height: 3.25rem;"><?= e(initials(OWNER)) ?></span>
            <h1 class="h3 mb-0">Sign in</h1>
        </div>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger py-2 small" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= panel('login') ?>">
            <?= csrf() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold" for="email">Email</label>
                <input class="form-control" id="email" name="email" type="email" value="<?= e($email) ?>" autofocus required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold" for="password">Password</label>
                <input class="form-control" id="password" name="password" type="password" required>
            </div>
            <button class="btn btn-primary w-100 btn-lg" type="submit">
                <?= icon('fa-solid fa-right-to-bracket') ?> Sign in
            </button>
        </form>

        <p class="text-center text-body-secondary small mt-4 mb-0">
            <a href="<?= url() ?>"><?= icon('fa-solid fa-arrow-left me-1') ?>Back to the website</a>
        </p>
    </div>
</div>
<script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
