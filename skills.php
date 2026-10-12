<?php
declare(strict_types=1);
require __DIR__ . '/app/init.php';

$title   = 'Skills';
$section = 'skills';
$all     = skills();

require __DIR__ . '/app/parts/header.php';
?>

<section class="container py-5">
    <span class="eyebrow">Skills &amp; tools</span>
    <h1 class="display-4 mt-2 mb-3">What I know, and what I'm learning</h1>
    <p class="lead" style="max-width: 42rem">
        Everything here has been used on something real — the languages I write,
        the stack and cloud tools I run it on, and the things I keep coming back
        to on my own machines.
    </p>
</section>

<?php
$barred = ['language', 'stack'];
foreach (categories() as $key => $heading):
    $rows = pick($all, $key);
    if ($rows === []) {
        continue;
    }
?>
    <section class="container pb-5">
        <div class="row g-4 g-lg-5">
            <div class="col-lg-4 reveal">
                <span class="eyebrow"><?= e($key) ?></span>
                <h2 class="h2 mt-2"><?= e($heading) ?></h2>
            </div>

            <div class="col-lg-8 reveal">
                <?php if (in_array($key, $barred, true)): ?>
                    <div class="tile p-4 d-grid gap-4">
                        <?php foreach ($rows as $skill): ?>
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold">
                                        <i class="<?= e($skill['icon']) ?> skill-icon me-1"></i><?= e($skill['name']) ?>
                                        <?php if ($skill['note'] !== ''): ?>
                                            <span class="small text-body-secondary fw-normal ms-2"><?= e($skill['note']) ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="mono small text-body-secondary"><?= (int) $skill['level'] ?>%</span>
                                </div>
                                <div class="meter"><span style="width: <?= (int) $skill['level'] ?>%"></span></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($rows as $skill): ?>
                            <span class="chip">
                                <?= icon($skill['icon']) ?><?= e($skill['name']) ?>
                                <?php if ($skill['note'] !== ''): ?>
                                    <span class="small text-body-secondary">— <?= e($skill['note']) ?></span>
                                <?php endif; ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endforeach; ?>

<section class="container pb-5">
    <div class="tile p-4 p-lg-5 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 reveal">
        <div>
            <h2 class="h3 mb-1">Curious how it looks in practice?</h2>
            <p class="text-body-secondary mb-0">The projects show these skills doing actual work.</p>
        </div>
        <a class="btn btn-primary btn-lg" href="<?= url('projects') ?>">
            <?= icon('fa-solid fa-diagram-project') ?> See the projects
        </a>
    </div>
</section>

<?php require __DIR__ . '/app/parts/footer.php'; ?>
