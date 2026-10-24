<?php
declare(strict_types=1);
require __DIR__ . '/app/init.php';

$title       = OWNER;
$section     = 'home';
$pageTitle   = OWNER . ' — Computer Engineering Student & Web Developer';
$description = 'Hiswill Kato is a Computer Engineering student at Busitema University and web developer, building practical web applications with PHP, MySQL, and Linux.';
$canonical   = 'https://hiswill.eu.org/';

$me   = me();
$term = terminal();

require __DIR__ . '/app/parts/header.php';
?>

<!-- =====================  HERO  ===================== -->
<section class="container py-5">
    <div class="row align-items-center g-4 g-lg-5">
        <div class="col-lg-6">
            <span class="eyebrow">Welcome</span>
            <h1 class="hero-name display-2 mt-3 mb-2">Hi, I'm <?= e($me['name'] ?? OWNER) ?>.</h1>
            <p class="fs-4 fw-semibold mb-3">
                <span class="aurora">Computer Engineering Student &amp; Web Developer</span>
            </p>
            <p class="lead mb-4" style="max-width: 34rem">
                I'm passionate about building useful web applications, exploring Linux systems,
                and using technology to solve everyday problems.
            </p>

            <div class="d-flex flex-wrap gap-2 mb-4">
                <a class="btn btn-primary btn-lg" href="<?= url('projects') ?>">
                    <?= icon('fa-solid fa-diagram-project') ?> Explore My Projects
                </a>
                <a class="btn btn-ghost btn-lg" href="<?= url('contact') ?>">
                    <?= icon('fa-regular fa-comment-dots') ?> Get in Touch
                </a>
            </div>

            <?php require __DIR__ . '/app/parts/socials.php'; ?>
        </div>

        <div class="col-lg-6">
            <div class="terminal reveal" id="terminal">
                <div class="terminal-bar">
                    <span class="terminal-dot red"></span>
                    <span class="terminal-dot amber"></span>
                    <span class="terminal-dot green"></span>
                    <span class="terminal-title"><?= e($term['host'] ?? 'hiswill@localhost') ?>: <?= e($term['path'] ?? '~') ?></span>
                </div>
                <div class="terminal-body">
                    <?= termHtml() ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =====================  ABOUT CTA  ===================== -->
<section class="container py-5">
    <div class="text-center reveal">
        <span class="eyebrow">About me</span>
        <h2 class="display-6 mb-2 mt-2">Get to know me</h2>
        <p class="lead mb-4" style="max-width: 40rem; margin-left: auto; margin-right: auto;">
            A little about my background, interests, and what I am working toward.
        </p>
        <a class="btn btn-primary btn-lg" href="<?= url('about') ?>">
            <?= icon('fa-solid fa-user') ?> Read my story
        </a>
    </div>
</section>

<!-- =====================  FEATURED PROJECTS  ===================== -->
<section class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4 reveal">
        <div>
            <span class="eyebrow">Selected work</span>
            <h2 class="display-6 mb-0 mt-2">Featured Projects</h2>
        </div>
        <a class="btn btn-ghost" href="<?= url('projects') ?>">
            See all projects <?= icon('fa-solid fa-arrow-right') ?>
        </a>
    </div>

    <div class="row g-4">
        <!-- Martazi -->
        <div class="col-md-6 col-lg-4 reveal">
            <article class="card work h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <span class="work-icon mb-3"><?= icon('fa-solid fa-cart-shopping') ?></span>
                    <h3 class="h5">Martazi</h3>
                    <p class="text-body-secondary flex-grow-1">
                        An e-commerce project focused on creating a practical and user-friendly online shopping experience.
                    </p>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <span class="badge rounded-pill text-bg-secondary">PHP</span>
                        <span class="badge rounded-pill text-bg-secondary">MySQL</span>
                        <span class="badge rounded-pill text-bg-secondary">Composer</span>
                    </div>
                    <a class="btn btn-ghost btn-sm mt-auto" href="https://github.com/HiswillKato/Martazi" target="_blank" rel="noopener">
                        <?= icon('fa-brands fa-github') ?> View on GitHub
                    </a>
                </div>
            </article>
        </div>

        <!-- SiFM -->
        <div class="col-md-6 col-lg-4 reveal">
            <article class="card work h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <span class="work-icon mb-3"><?= icon('fa-solid fa-folder-tree') ?></span>
                    <h3 class="h5">SiFM — Simple File Manager</h3>
                    <p class="text-body-secondary flex-grow-1">
                        A lightweight, single-file PHP web application for managing files through a browser.
                    </p>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <span class="badge rounded-pill text-bg-secondary">PHP</span>
                        <span class="badge rounded-pill text-bg-secondary">HTML</span>
                        <span class="badge rounded-pill text-bg-secondary">CSS</span>
                        <span class="badge rounded-pill text-bg-secondary">JavaScript</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-auto">
                        <a class="btn btn-primary btn-sm" href="<?= url('projects/sifm') ?>">
                            <?= icon('fa-solid fa-book-open') ?> Details
                        </a>
                        <a class="btn btn-ghost btn-sm" href="https://github.com/HiswillKato/SiFM" target="_blank" rel="noopener">
                            <?= icon('fa-brands fa-github') ?> Code
                        </a>
                    </div>
                </div>
            </article>
        </div>

        <!-- Gospelzora -->
        <div class="col-md-6 col-lg-4 reveal">
            <article class="card work h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <span class="work-icon mb-3"><?= icon('fa-solid fa-music') ?></span>
                    <h3 class="h5">Gospelzora</h3>
                    <p class="text-body-secondary flex-grow-1">
                        A gospel music platform inspired by the vision of transforming lives through worship.
                        Gospelzora is currently maintained on GitHub and is not publicly deployed.
                    </p>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <span class="badge rounded-pill text-bg-secondary">PHP</span>
                        <span class="badge rounded-pill text-bg-secondary">MySQL</span>
                        <span class="badge rounded-pill text-bg-secondary">Bootstrap 5</span>
                        <span class="badge rounded-pill text-bg-secondary">JavaScript</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-auto">
                        <a class="btn btn-primary btn-sm" href="<?= url('projects/gospelzora') ?>">
                            <?= icon('fa-solid fa-book-open') ?> Details
                        </a>
                        <a class="btn btn-ghost btn-sm" href="https://github.com/HiswillKato/Gospelzora" target="_blank" rel="noopener">
                            <?= icon('fa-brands fa-github') ?> Code
                        </a>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- =====================  SKILLS  ===================== -->
<section class="container py-5">
    <div class="row g-4 g-lg-5 align-items-center">
        <div class="col-lg-5 reveal">
            <span class="eyebrow">What I work with</span>
            <h2 class="display-6 mt-2 mb-3">What I Work With</h2>
            <p class="text-body-secondary">
                A focused set of tools I use to design, build, and run web applications and Linux-based systems.
            </p>
            <a class="btn btn-ghost" href="<?= url('skills') ?>">
                <?= icon('fa-solid fa-layer-group') ?> See all skills
            </a>
        </div>
        <div class="col-lg-7 reveal">
            <div class="tile p-4 d-flex flex-wrap gap-2">
                <?php
                $skillItems = [
                    ['PHP', 'fa-brands fa-php'],
                    ['MySQL', 'fa-solid fa-database'],
                    ['HTML & CSS', 'fa-brands fa-html5'],
                    ['JavaScript', 'fa-brands fa-js'],
                    ['Bootstrap', 'fa-brands fa-bootstrap'],
                    ['Linux', 'fa-brands fa-linux'],
                    ['Nginx', 'fa-solid fa-server'],
                    ['Git & GitHub', 'fa-brands fa-github'],
                ];
                foreach ($skillItems as [$name, $icon]): ?>
                    <span class="chip"><?= icon($icon) ?><?= e($name) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- =====================  CONTACT  ===================== -->
<section class="container pb-5">
    <div class="text-center reveal">
        <span class="eyebrow">Let's connect</span>
        <h2 class="display-6 mb-2 mt-2">Let's Connect</h2>
        <p class="lead mb-4" style="max-width: 40rem; margin-left: auto; margin-right: auto;">
            Interested in my work, have an idea to share, or just want to say hello? Feel free to reach out.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
            <a class="btn btn-primary btn-lg" href="<?= url('contact') ?>">
                <?= icon('fa-solid fa-paper-plane') ?> Get in Touch
            </a>
            <a class="btn btn-ghost btn-lg" href="https://github.com/HiswillKato" target="_blank" rel="noopener">
                <?= icon('fa-brands fa-github') ?> GitHub
            </a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/app/parts/footer.php'; ?>
