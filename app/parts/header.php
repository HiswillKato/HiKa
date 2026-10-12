<?php
/* The masthead and the opening of <main>. A page sets `$title` and `$section`
   before requiring this file; nothing else is expected of it. */
$me     = me();
$label  = $title ?? OWNER;
$active = $section ?? '';

$nav = [
    'home'     => ['Home', ''],
    'about'    => ['About', 'about'],
    'skills'   => ['Skills', 'skills'],
    'projects' => ['Projects', 'projects'],
    'contact'  => ['Contact', 'contact'],
];
?>
<!doctype html>
<html lang="en" data-theme="dark" data-bs-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $label === OWNER ? e(OWNER) . ' — ' . e($me['title'] ?? '') : e($label) . ' · ' . e(OWNER) ?></title>
<meta name="description" content="<?= e($me['tagline'] ?? ('The personal website of ' . OWNER)) ?>">
<meta name="author" content="<?= e($me['name'] ?? OWNER) ?>">
<meta name="theme-color" content="#070c11">
<link rel="icon" href="<?= asset('images/favicon.svg') ?>" type="image/svg+xml">
<link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= asset('css/all.min.css') ?>">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
<script>
/* Choose the theme before the first paint, so there is never a flash of the
   wrong colour. Anything but a stored "light" results in the dark default. */
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
<a class="visually-hidden-focusable" href="#main">Skip to content</a>

<header class="site-header">
    <nav class="navbar navbar-expand-lg" aria-label="Main">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url() ?>">
                <span class="brand-mark"><?= e(initials($me['name'] ?? OWNER)) ?></span>
                <span class="d-flex flex-column">
                    <span class="brand-name"><?= e($me['name'] ?? OWNER) ?></span>
                    <span class="brand-tagline d-none d-sm-block"><?= e($me['title'] ?? '') ?></span>
                </span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navmenu"
                    aria-controls="navmenu" aria-expanded="false" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navmenu">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1 mt-3 mt-lg-0">
                    <?php foreach ($nav as $key => [$text, $path]): ?>
                        <li class="nav-item">
                            <a class="nav-link<?= $active === $key ? ' active' : '' ?>"
                               <?= $active === $key ? 'aria-current="page"' : '' ?>
                               href="<?= url($path) ?>"><?= e($text) ?></a>
                        </li>
                    <?php endforeach; ?>

                    <li class="nav-item ms-lg-2">
                        <button type="button"
                                class="btn btn-ghost btn-sm rounded-circle theme-toggle"
                                id="theme-toggle" aria-label="Switch theme">
                            <i class="fa-solid fa-moon"></i>
                            <i class="fa-solid fa-sun"></i>
                        </button>
                    </li>
                    <li class="nav-item mt-2 mt-lg-0 ms-lg-2">
                        <a class="btn btn-primary" href="<?= url('contact') ?>">
                            <?= icon('fa-solid fa-paper-plane') ?> Let's talk
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main id="main">
