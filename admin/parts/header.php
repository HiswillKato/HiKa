<?php
/* The admin shell: a dark offcanvas sidebar that becomes static on wide
   screens, a top bar, and the opening of the working area. Every screen here
   is behind guard(), so nothing renders without a signed-in user. */
$user = guard();
$me   = me();
$here = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php', '.php');
$label = $title ?? 'Dashboard';

$menu = [
    ['index',    ['index'],                     'Dashboard', 'fa-solid fa-gauge-high'],
    ['projects', ['projects', 'project'],       'Projects',  'fa-solid fa-diagram-project'],
    ['skills',   ['skills', 'skill'],           'Skills',    'fa-solid fa-layer-group'],
    ['socials',  ['socials', 'social'],         'Socials',   'fa-solid fa-share-nodes'],
    ['timelines', ['timelines', 'timeline'],    'Timeline',  'fa-solid fa-timeline'],
    ['messages', ['messages', 'message'],       'Messages',  'fa-solid fa-envelope'],
    ['terminal', ['terminal'],                  'Terminal',  'fa-solid fa-terminal'],
    ['settings', ['settings'],                  'Settings',  'fa-solid fa-gear'],
    ['profile',  ['profile'],                   'Profile',   'fa-solid fa-id-badge'],
];
$unread = total('SELECT COUNT(*) FROM messages WHERE seen = 0');
?>
<!doctype html>
<html lang="en" data-theme="dark" data-bs-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($label) ?> · Admin · <?= e(OWNER) ?></title>
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
<div class="admin-shell d-flex">

    <div class="offcanvas-lg offcanvas-start admin-side" id="sidebar" tabindex="-1" aria-label="Admin menu">
        <div class="offcanvas-header border-bottom">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url() ?>">
                <span class="brand-mark"><?= e(initials($me['name'] ?? OWNER)) ?></span>
                <span class="d-flex flex-column">
                    <span class="brand-name"><?= e($me['name'] ?? OWNER) ?></span>
                    <span class="brand-tagline">Admin panel</span>
                </span>
            </a>
            <button class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column p-3">
            <nav class="d-grid gap-1">
                <?php foreach ($menu as [$key, $scripts, $text, $glyph]): ?>
                    <?php $active = in_array($here, $scripts, true); ?>
                    <a class="admin-link<?= $active ? ' active' : '' ?>" href="<?= panel($key === 'index' ? '' : $key) ?>">
                        <i class="<?= e($glyph) ?>"></i>
                        <span><?= e($text) ?></span>
                        <?php if ($key === 'messages' && $unread > 0): ?>
                            <span class="badge rounded-pill text-bg-warning ms-auto"><?= $unread ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <hr class="my-3">

            <nav class="d-grid gap-1">
                <a class="admin-link" href="<?= url() ?>" target="_blank" rel="noopener">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i><span>View site</span>
                </a>
                <a class="admin-link" href="<?= panel('logout') ?>">
                    <i class="fa-solid fa-right-from-bracket"></i><span>Sign out</span>
                </a>
            </nav>

            <div class="mt-auto pt-3 small text-body-secondary">
                Signed in as<br><span class="fw-semibold text-body"><?= e($user['name']) ?></span>
            </div>
        </div>
    </div>

    <div class="flex-grow-1 min-width-0">
        <header class="d-flex align-items-center gap-3 px-3 px-lg-4 py-3 border-bottom"
                style="background: var(--surface); position: sticky; top: 0; z-index: 1020;">
            <button class="btn btn-ghost rounded-circle d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Menu">
                <?= icon('fa-solid fa-bars') ?>
            </button>
            <h1 class="h4 mb-0"><?= e($label) ?></h1>
            <div class="ms-auto d-flex align-items-center gap-2">
                <?php if (!empty($actions)): ?>
                    <div class="d-flex flex-wrap gap-2"><?= $actions ?></div>
                <?php endif; ?>
                <?= themeToggle() ?>
            </div>
        </header>

        <div class="p-3 p-lg-4">
            <?= notice() ?>
