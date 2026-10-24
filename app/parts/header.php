<?php
/* The masthead and the opening of <main>. A page sets `$title` and `$section`
   before requiring this file; nothing else is expected of it. */
$me     = me();
$label  = $title ?? OWNER;
$active = $section ?? '';

/* What search engines and link previews are told. A page may set its own
   `$description`; otherwise the profile tagline stands in for all of them. */
$full  = $label === OWNER ? OWNER . ' — ' . ($me['title'] ?? '') : $label . ' · ' . OWNER;
if (isset($pageTitle) && $pageTitle !== '') {
    $full = $pageTitle;
}
$about = $me['tagline'] ?? ('The personal website of ' . OWNER);
$desc  = $description ?? $about;

/* The one canonical address for this page, with any query string dropped, so
   the same content is never indexed under two spellings. */
$here = strtok((string) ($_SERVER['REQUEST_URI'] ?? '/'), '?') ?: '/';
if (isset($canonical) && $canonical !== '') {
    $link = $canonical;
} else {
    $link = url(trim(preg_replace('#\.php$#', '', $here), '/'));
}
$shot = asset('images/og.png');

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
<title><?= e($full) ?></title>
<meta name="description" content="<?= e($desc) ?>">
<meta name="author" content="<?= e($me['name'] ?? OWNER) ?>">
<link rel="canonical" href="<?= e($link) ?>">
<meta name="theme-color" content="#070c11">
<!-- Ask for the largest preview a search engine will give us rather than the
     thumbnail, and allow a full snippet. Everything here is meant to be found. -->
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

<!-- How the page is introduced when it is shared: Open Graph for most apps,
     the Twitter card for X. -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e(OWNER) ?>">
<meta property="og:title" content="<?= e($full) ?>">
<meta property="og:description" content="<?= e($desc) ?>">
<meta property="og:url" content="<?= e($link) ?>">
<meta property="og:image" content="<?= e($shot) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:type" content="image/png">
<meta property="og:image:alt" content="<?= e(($me['name'] ?? OWNER) . ' — ' . ($me['title'] ?? '')) ?>">
<meta property="og:locale" content="en_GB">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($full) ?>">
<meta name="twitter:description" content="<?= e($desc) ?>">
<meta name="twitter:image" content="<?= e($shot) ?>">
<link rel="icon" href="<?= asset('images/favicon.svg') ?>" type="image/svg+xml">
<link rel="apple-touch-icon" href="<?= asset('images/apple-touch-icon.png') ?>">
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

<?php
/* A machine-readable card for search engines: who the site is about, where
   they study, and the profiles that mirror them. Built from the same rows the
   pages show, so it can never drift from the content. Empty pieces drop out. */
$place = array_values(array_filter(array_map('trim', explode(',', (string) ($me['location'] ?? '')))));

$card = array_filter([
    '@context'      => 'https://schema.org',
    '@type'         => 'Person',
    'name'          => $me['name'] ?? OWNER,
    'alternateName' => ALIAS,
    'url'           => url(),
    'jobTitle'      => $me['title'] ?? '',
    'description'   => $about,
    'email'         => 'mailto:' . ($me['email'] ?? EMAIL),
    'alumniOf'      => ($me['campus'] ?? '') === '' ? null : ['@type' => 'CollegeOrUniversity', 'name' => $me['campus']],
    'address'       => $place === [] ? null : array_filter([
        '@type'           => 'PostalAddress',
        'addressLocality' => $place[0] ?? null,
        'addressRegion'   => $place[1] ?? null,
        'addressCountry'  => $place[2] ?? 'UG',
    ]),
    'sameAs'        => array_values(array_filter(array_column(socials(), 'url'))),
]);
?>
<script type="application/ld+json"><?= json_encode($card, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<?php if (!empty($schema)): ?>
<?php /* A page may hand in one more card of its own — a project, say — which is
   printed beside the person. It arrives already built, so its shape is the
   page's business, not this file's. */ ?>
<script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<?php endif; ?>
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

            <?= themeToggle('d-lg-none ms-auto') ?>

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

                    <li class="nav-item d-none d-lg-block ms-lg-2">
                        <?= themeToggle('btn-sm') ?>
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
