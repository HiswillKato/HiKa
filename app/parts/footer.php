<?php
$me      = me();
$socials = socials();
?>
</main>

<footer class="site-footer mt-5">
    <div class="container py-5">
        <div class="row g-4 g-lg-5">
            <div class="col-lg-5">
                <a class="navbar-brand d-inline-flex align-items-center gap-2 mb-3" href="<?= url() ?>">
                    <span class="brand-mark"><?= e(initials($me['name'] ?? OWNER)) ?></span>
                    <span class="brand-name"><?= e($me['name'] ?? OWNER) ?></span>
                </a>
                <p class="text-body-secondary mb-4" style="max-width: 30rem">
                    <?= e($me['tagline'] ?? '') ?>
                </p>
                <?php if ($socials !== []): ?>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($socials as $item): ?>
                            <a class="social" href="<?= e($item['url'] ?: url('contact')) ?>"
                               <?= $item['url'] !== '' ? 'target="_blank" rel="noopener"' : '' ?>
                               style="--brand: <?= e($item['color'] ?: 'var(--bs-primary)') ?>"
                               title="<?= e($item['name']) ?>" aria-label="<?= e($item['name']) ?>">
                                <?= icon($item['icon']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-6 col-lg-3">
                <h6 class="eyebrow mb-3">Explore</h6>
                <ul class="list-unstyled d-grid gap-2 mb-0">
                    <li><a class="link-body-emphasis text-decoration-none" href="<?= url('about') ?>">About</a></li>
                    <li><a class="link-body-emphasis text-decoration-none" href="<?= url('skills') ?>">Skills</a></li>
                    <li><a class="link-body-emphasis text-decoration-none" href="<?= url('projects') ?>">Projects</a></li>
                    <li><a class="link-body-emphasis text-decoration-none" href="<?= url('contact') ?>">Contact</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-4">
                <h6 class="eyebrow mb-3">Get in touch</h6>
                <ul class="list-unstyled d-grid gap-2 mb-0">
                    <li>
                        <a class="link-body-emphasis text-decoration-none text-break" href="mailto:<?= e($me['email'] ?? EMAIL) ?>">
                            <?= icon('fa-solid fa-envelope me-2 text-primary') ?><?= e($me['email'] ?? EMAIL) ?>
                        </a>
                    </li>
                    <li>
                        <a class="link-body-emphasis text-decoration-none text-break" href="tel:<?= e(preg_replace('/\s+/', '', $me['phone'] ?? '')) ?>">
                            <?= icon('fa-solid fa-phone me-2 text-primary') ?><?= e($me['phone'] ?? '') ?>
                        </a>
                    </li>
                    <li class="text-body-secondary">
                        <?= icon('fa-solid fa-location-dot me-2 text-primary') ?><?= e($me['location'] ?? '') ?>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 small text-body-secondary">
            <span>&copy; <span data-year><?= date('Y') ?></span> <?= e($me['name'] ?? OWNER) ?>. All rights reserved.</span>
            <span class="mono">Built with PHP, Bootstrap &amp; Font Awesome — running on Ubuntu &amp; Apache.</span>
        </div>
    </div>
</footer>

<button id="top" class="btn btn-primary rounded-circle" hidden aria-label="Back to top">
    <?= icon('fa-solid fa-arrow-up') ?>
</button>

<script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
