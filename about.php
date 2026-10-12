<?php
declare(strict_types=1);
require __DIR__ . '/app/init.php';

$title   = 'About';
$section = 'about';
$me      = me();

require __DIR__ . '/app/parts/header.php';
?>

<section class="container py-5">
    <span class="eyebrow">About me</span>
    <h1 class="display-4 mt-2 mb-0">The person behind the keyboard</h1>
</section>

<section class="container pb-5">
    <div class="row g-4 g-lg-5">
        <!-- ---------- profile card ---------- -->
        <div class="col-lg-4">
            <div class="card p-4 reveal" style="position: sticky; top: 6rem;">
                <div class="text-center mb-3">
                    <?php if (!empty($me['avatar'])): ?>
                        <img src="<?= upload($me['avatar']) ?>" alt="<?= e($me['name'] ?? '') ?>"
                             class="rounded-circle mx-auto d-block" style="width: 8rem; height: 8rem; object-fit: cover;"
                             width="128" height="128">
                    <?php else: ?>
                        <span class="brand-mark mx-auto d-flex" style="width: 6rem; height: 6rem; font-size: 2rem;">
                            <?= e(initials($me['name'] ?? OWNER)) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <h2 class="h4 text-center mb-1"><?= e($me['name'] ?? OWNER) ?></h2>
                <p class="text-center text-body-secondary mb-4"><?= e($me['title'] ?? '') ?></p>

                <ul class="list-unstyled d-grid gap-3 small mb-4">
                    <li class="d-flex gap-2"><?= icon('fa-solid fa-location-dot text-primary mt-1') ?><?= e($me['location'] ?? '') ?></li>
                    <li class="d-flex gap-2"><?= icon('fa-solid fa-graduation-cap text-primary mt-1') ?><?= e($me['campus'] ?? '') ?></li>
                    <li class="d-flex gap-2"><?= icon('fa-solid fa-book text-primary mt-1') ?><?= e($me['degree'] ?? '') ?></li>
                </ul>

                <div class="d-grid gap-2">
                    <a class="btn btn-primary" href="mailto:<?= e($me['email'] ?? EMAIL) ?>">
                        <?= icon('fa-solid fa-envelope') ?> Email me
                    </a>
                    <a class="btn btn-ghost" href="<?= url('contact') ?>">
                        <?= icon('fa-solid fa-paper-plane') ?> Contact page
                    </a>
                </div>
            </div>
        </div>

        <!-- ---------- the story ---------- -->
        <div class="col-lg-8">
            <div class="reveal" style="font-size: 1.06rem;">
                <?= prose($me['about'] ?? '', headings: false) ?>
            </div>

            <?php $interest = pick(skills(), 'interest'); ?>
            <?php if ($interest !== []): ?>
                <h2 class="h4 mt-5 mb-3 reveal">What I'm into</h2>
                <div class="d-flex flex-wrap gap-2 reveal">
                    <?php foreach ($interest as $item): ?>
                        <span class="chip"><?= icon($item['icon']) ?><?= e($item['name']) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- =====================  TIMELINE  ===================== -->
<?php $journey = timeline(); ?>
<?php if ($journey !== []): ?>
    <section class="container py-5">
        <span class="eyebrow">The journey</span>
        <h2 class="display-6 mt-2 mb-4">Where I've been</h2>

        <div class="timeline reveal">
            <?php foreach ($journey as $item): ?>
                <div class="timeline-item">
                    <div class="timeline-period mb-1">
                        <?= e($item['period']) ?>
                        &middot; <?= e(ucfirst($item['type'])) ?>
                    </div>
                    <h3 class="h5 mb-1"><?= e($item['title']) ?></h3>
                    <?php if ($item['place'] !== ''): ?>
                        <div class="fw-semibold text-body-secondary mb-1"><?= e($item['place']) ?></div>
                    <?php endif; ?>
                    <?php if ($item['detail'] !== ''): ?>
                        <p class="text-body-secondary mb-0"><?= e($item['detail']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="container pb-5">
    <div class="tile p-4 p-lg-5 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 reveal">
        <div>
            <h2 class="h3 mb-1">Want the short version?</h2>
            <p class="text-body-secondary mb-0">See the tools I use, or jump straight to my projects.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-primary" href="<?= url('skills') ?>"><?= icon('fa-solid fa-layer-group') ?> Skills</a>
            <a class="btn btn-ghost" href="<?= url('projects') ?>"><?= icon('fa-solid fa-diagram-project') ?> Projects</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/app/parts/footer.php'; ?>
