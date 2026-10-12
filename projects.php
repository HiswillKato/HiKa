<?php
declare(strict_types=1);
require __DIR__ . '/app/init.php';

$title   = 'Projects';
$section = 'projects';
$items   = projects();

require __DIR__ . '/app/parts/header.php';
?>

<section class="container py-5">
    <span class="eyebrow">Projects</span>
    <h1 class="display-4 mt-2 mb-3">Things I've designed, built and shipped</h1>
    <p class="lead" style="max-width: 42rem">
        A selection of the work I'm happy to put my name on. Most are open source
        and live on GitHub — take a look, open an issue, or say hello.
    </p>
</section>

<section class="container pb-5">
    <?php if ($items === []): ?>
        <div class="tile p-5 text-center">
            <?= icon('fa-regular fa-folder-open fa-2x text-primary mb-3') ?>
            <p class="mb-0 text-body-secondary">No projects to show yet — check back soon.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($items as $item): ?>
                <div class="col-md-6 col-lg-4 reveal">
                    <article class="card work">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <span class="work-icon"><?= icon($item['icon']) ?></span>
                                <?php if (!empty($item['featured'])): ?>
                                    <span class="badge rounded-pill text-bg-warning"><?= icon('fa-solid fa-star me-1') ?>Featured</span>
                                <?php endif; ?>
                            </div>

                            <h2 class="h5 mb-2"><?= e($item['title']) ?></h2>
                            <p class="text-body-secondary flex-grow-1"><?= e(cut($item['summary'], 140)) ?></p>

                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <?php foreach (array_filter(array_map('trim', explode(',', $item['tech']))) as $tech): ?>
                                    <span class="badge rounded-pill text-bg-secondary fw-normal"><?= e($tech) ?></span>
                                <?php endforeach; ?>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-auto pt-2 border-top" style="position: relative; z-index: 2;">
                                <a class="btn btn-primary btn-sm" href="<?= url('projects/' . $item['slug']) ?>">
                                    <?= icon('fa-solid fa-book-open') ?> Details
                                </a>
                                <?php if (!empty($item['repo'])): ?>
                                    <a class="btn btn-ghost btn-sm" href="<?= e($item['repo']) ?>" target="_blank" rel="noopener">
                                        <?= icon('fa-brands fa-github') ?> Code
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($item['live'])): ?>
                                    <a class="btn btn-ghost btn-sm" href="<?= e($item['live']) ?>" target="_blank" rel="noopener">
                                        <?= icon('fa-solid fa-arrow-up-right-from-square') ?> Live
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/app/parts/footer.php'; ?>
