<?php
declare(strict_types=1);
require __DIR__ . '/app/init.php';

$title   = OWNER;
$section = 'home';
$me      = me();

require __DIR__ . '/app/parts/header.php';
?>

<!-- =====================  HERO  ===================== -->
<section class="container py-5">
    <div class="row align-items-center g-5">
        <div class="col-lg-6">
            <span class="eyebrow">Hello, I'm</span>
            <h1 class="hero-name display-2 mt-3 mb-2"><?= e($me['name'] ?? OWNER) ?></h1>
            <p class="fs-4 fw-semibold mb-3">
                <span class="aurora"><?= e($me['title'] ?? '') ?></span>
            </p>
            <p class="lead mb-4" style="max-width: 34rem"><?= e($me['tagline'] ?? '') ?></p>

            <?php if (!empty($me['available'])): ?>
                <p class="d-inline-flex align-items-center gap-2 mb-4 px-3 py-1 rounded-pill border"
                   style="background: var(--surface);">
                    <span class="d-inline-block rounded-circle bg-primary" style="width:.55rem;height:.55rem"></span>
                    <span class="small fw-semibold">Available for work &amp; collaboration</span>
                </p>
            <?php endif; ?>

            <div class="d-flex flex-wrap gap-2 mb-4">
                <a class="btn btn-primary btn-lg" href="<?= url('projects') ?>">
                    <?= icon('fa-solid fa-diagram-project') ?> View my work
                </a>
                <a class="btn btn-ghost btn-lg" href="<?= url('contact') ?>">
                    <?= icon('fa-regular fa-comment-dots') ?> Get in touch
                </a>
            </div>

            <?php require __DIR__ . '/app/parts/socials.php'; ?>
        </div>

        <div class="col-lg-6">
            <div class="terminal reveal">
                <div class="terminal-bar">
                    <span class="terminal-dot red"></span>
                    <span class="terminal-dot amber"></span>
                    <span class="terminal-dot green"></span>
                    <span class="terminal-title">hiswill@localhost: ~</span>
                </div>
                <div class="terminal-body">
                    <div><span class="prompt">hiswill@localhost</span>:<span class="path">~</span>$ whoami</div>
                    <div class="out"><?= e($me['name'] ?? OWNER) ?> — <?= e($me['title'] ?? '') ?></div>

                    <div class="mt-3"><span class="prompt">hiswill@localhost</span>:<span class="path">~</span>$ <span class="arg">cat</span> interests.txt</div>
                    <div class="out">&gt; <span data-typing='["Linux &amp; Distros","Cloud &amp; AWS","Bash Scripting","Web Development","Artificial Intelligence","Systems Security"]'></span><span class="caret"></span></div>

                    <div class="mt-3"><span class="prompt">hiswill@localhost</span>:<span class="path">~</span>$ <span class="arg">ls</span> projects/</div>
                    <div class="out">BUBAPC&nbsp;&nbsp;Gospelzora&nbsp;&nbsp;SiFM</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =====================  QUICK FACTS  ===================== -->
<section class="container pb-4">
    <div class="row g-3 reveal">
        <div class="col-6 col-lg-3">
            <div class="tile h-100 p-3 d-flex align-items-center gap-3">
                <span class="work-icon"><?= icon('fa-solid fa-code') ?></span>
                <div>
                    <div class="h4 mb-0"><?= date('Y') - 2020 ?>+</div>
                    <div class="small text-body-secondary">Years coding</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="tile h-100 p-3 d-flex align-items-center gap-3">
                <span class="work-icon"><?= icon('fa-brands fa-aws') ?></span>
                <div>
                    <div class="h4 mb-0">AWS</div>
                    <div class="small text-body-secondary">Learning the cloud</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="tile h-100 p-3 d-flex align-items-center gap-3">
                <span class="work-icon"><?= icon('fa-brands fa-github') ?></span>
                <div>
                    <div class="h4 mb-0"><?= total("SELECT COUNT(*) FROM projects WHERE status = 'published'") ?></div>
                    <div class="small text-body-secondary">Public projects</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="tile h-100 p-3 d-flex align-items-center gap-3">
                <span class="work-icon"><?= icon('fa-solid fa-graduation-cap') ?></span>
                <div>
                    <div class="h4 mb-0">Busitema</div>
                    <div class="small text-body-secondary">University</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =====================  FEATURED WORK  ===================== -->
<section class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4 reveal">
        <div>
            <span class="eyebrow">Selected work</span>
            <h2 class="display-6 mb-0 mt-2">Things I've built</h2>
        </div>
        <a class="btn btn-ghost" href="<?= url('projects') ?>">
            All projects <?= icon('fa-solid fa-arrow-right') ?>
        </a>
    </div>

    <div class="row g-4">
        <?php foreach (featured(3) as $item): ?>
            <div class="col-md-6 col-lg-4 reveal">
                <article class="card work">
                    <div class="card-body p-4 d-flex flex-column">
                        <span class="work-icon mb-3"><?= icon($item['icon']) ?></span>
                        <h3 class="h5"><?= e($item['title']) ?></h3>
                        <p class="text-body-secondary flex-grow-1"><?= e(cut($item['summary'], 130)) ?></p>
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            <?php foreach (array_slice(array_map('trim', explode(',', $item['tech'])), 0, 3) as $tech): ?>
                                <span class="badge rounded-pill text-bg-secondary"><?= e($tech) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <a class="stretched-link fw-semibold" href="<?= url('projects/' . $item['slug']) ?>">
                            Read more <?= icon('fa-solid fa-arrow-right ms-1') ?>
                        </a>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- =====================  SKILLS SNAPSHOT  ===================== -->
<section class="container py-5">
    <div class="row g-5 align-items-center">
        <div class="col-lg-5 reveal">
            <span class="eyebrow">What I work with</span>
            <h2 class="display-6 mt-2 mb-3">A small stack, used well.</h2>
            <p class="text-body-secondary">
                I reach for the smallest tool that does the job — plain PHP and MySQL
                for web software, Bash for the repetitive parts, and Linux to run it
                all. Here are the languages I use most.
            </p>
            <a class="btn btn-ghost" href="<?= url('skills') ?>">
                <?= icon('fa-solid fa-layer-group') ?> See all skills
            </a>
        </div>
        <div class="col-lg-7 reveal">
            <div class="tile p-4 d-grid gap-4">
                <?php foreach (array_slice(pick(skills(), 'language'), 0, 5) as $skill): ?>
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">
                                <i class="<?= e($skill['icon']) ?> skill-icon me-1"></i><?= e($skill['name']) ?>
                            </span>
                            <span class="mono small text-body-secondary"><?= (int) $skill['level'] ?>%</span>
                        </div>
                        <div class="meter"><span style="width: <?= (int) $skill['level'] ?>%"></span></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- =====================  CTA  ===================== -->
<section class="container pb-5">
    <div class="tile p-4 p-lg-5 text-center reveal"
         style="background-image: linear-gradient(120deg, rgba(var(--bs-primary-rgb),.12), rgba(var(--sky-rgb),.10));">
        <h2 class="display-6 mb-2">Let's build something — or just talk shop.</h2>
        <p class="lead mb-4">I'm open to freelance work, collaboration, and a good conversation about code, the cloud or Linux.</p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a class="btn btn-primary btn-lg" href="<?= url('contact') ?>">
                <?= icon('fa-solid fa-paper-plane') ?> Start a conversation
            </a>
            <a class="btn btn-ghost btn-lg" href="<?= url('about') ?>">
                More about me
            </a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/app/parts/footer.php'; ?>
