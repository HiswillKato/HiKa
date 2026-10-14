<?php
declare(strict_types=1);

/* =========================================================================
   START UP
   The single line every page begins with. It loads the pieces in the order
   they depend on each other, opens the session, and installs one handler so
   an unexpected error becomes a tidy page rather than a blank screen.
   ========================================================================= */

require __DIR__ . '/config.php';
require __DIR__ . '/db.php';
require __DIR__ . '/functions.php';
require __DIR__ . '/data.php';

error_reporting(SHOW_ERRORS ? E_ALL : 0);
ini_set('display_errors', SHOW_ERRORS ? '1' : '0');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Maintenance mode: visitors get a short notice, while the admin panel and a
   signed-in owner are let through, so the site can be worked on and previewed.
   Assets are static files and never pass through here, so they keep loading. */
if (flag('maintenance') && owner() === null
    && !str_starts_with($_SERVER['SCRIPT_NAME'] ?? '', '/admin/')) {
    http_response_code(503);
    $title   = 'Maintenance';
    $section = '';

    require __DIR__ . '/parts/header.php';
    ?>
    <section class="container py-5 text-center" style="max-width: 44rem;">
        <span class="work-icon mb-3 d-inline-flex"><?= icon('fa-solid fa-gear') ?></span>
        <h1 class="display-4 mb-3">Down for maintenance</h1>
        <p class="lead mb-0">The site is getting a quick tune-up. It will be back shortly — nothing is broken, just being polished.</p>
    </section>
    <?php
    require __DIR__ . '/parts/footer.php';
    exit;
}

set_exception_handler(static function (Throwable $problem): void {
    error_log($problem->getMessage() . ' in ' . $problem->getFile() . ':' . $problem->getLine());

    http_response_code(500);
    $detail = SHOW_ERRORS
        ? '<pre class="small text-start p-3 bg-body-tertiary rounded-3">' . e($problem->getMessage() . "\n" . $problem->getFile() . ':' . $problem->getLine()) . '</pre>'
        : '';

    exit('<!doctype html><html lang="en"><meta charset="utf-8"><title>Something went wrong</title>'
        . '<link href="' . asset('css/bootstrap.min.css') . '" rel="stylesheet">'
        . '<body class="bg-body"><div class="container" style="max-width:44rem;padding-top:16vh">'
        . '<h1 class="h3">Something went wrong on our side.</h1>'
        . '<p class="text-body-secondary">The problem has been logged. You can head back to the '
        . '<a href="' . url() . '">home page</a> and try again.</p>'
        . $detail
        . '</div></body></html>');
});
