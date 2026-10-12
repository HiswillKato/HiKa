<?php
declare(strict_types=1);
require __DIR__ . '/app/init.php';

http_response_code(404);
$title   = 'Not found';
$section = '';

require __DIR__ . '/app/parts/header.php';
?>

<section class="container py-5 text-center" style="max-width: 44rem;">
    <!--<div class="mono text-primary mb-3">hiswill@localhost:~$ cat .cursor</div>-->
    <h1 class="hero-name display-1 mb-3">404</h1>
    <p class="lead mb-4">
        That page ran off. The address may be wrong, or the page has moved.
    </p>
    <div class="d-flex flex-wrap justify-content-center gap-2">
        <a class="btn btn-primary btn-lg" href="<?= url() ?>">
            <?= icon('fa-solid fa-house') ?> Back home
        </a>
        <a class="btn btn-ghost btn-lg" href="<?= url('contact') ?>">
            <?= icon('fa-solid fa-paper-plane') ?> Contact me
        </a>
    </div>
</section>

<?php require __DIR__ . '/app/parts/footer.php'; ?>
