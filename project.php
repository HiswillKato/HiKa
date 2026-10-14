<?php
declare(strict_types=1);
require __DIR__ . '/app/init.php';

$whose = trim((string) ($_GET['slug'] ?? ''));
$work  = $whose === '' ? null : project($whose);

if ($work === null) {
    http_response_code(404);
    $title   = 'Not found';
    $section = 'projects';
    require __DIR__ . '/app/parts/header.php';
    ?>
    <section class="container py-5 text-center" style="max-width: 40rem;">
        <div class="eyebrow mb-2">Error 404</div>
        <h1 class="display-4 mb-3">That project doesn't exist</h1>
        <p class="lead mb-4">The address may be wrong, or the project has been taken down.</p>
        <a class="btn btn-primary" href="<?= url('projects') ?>">
            <?= icon('fa-solid fa-arrow-left') ?> Back to all projects
        </a>
    </section>
    <?php
    require __DIR__ . '/app/parts/footer.php';
    exit;
}

$title       = $work['title'];
$section     = 'projects';
$description = $work['summary'];
$tech        = array_filter(array_map('trim', explode(',', $work['tech'])));

$others = array_values(array_filter(projects(), static fn (array $row): bool => $row['id'] !== $work['id']));
$others = array_slice($others, 0, 3);

/* What a search engine is told this page is: the work itself, and the trail
   back to the list it came from. The header prints it beside the person. */
$schema = [
    '@context' => 'https://schema.org',
    '@graph'   => [
        array_filter([
            '@type'          => 'CreativeWork',
            'name'           => $work['title'],
            'description'    => $work['summary'],
            'url'            => url('projects/' . $work['slug']),
            'codeRepository' => $work['repo'] !== '' ? $work['repo'] : null,
            'keywords'       => $work['tech'],
            'author'         => ['@type' => 'Person', 'name' => OWNER, 'url' => url()],
        ]),
        [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Projects', 'item' => url('projects')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $work['title'], 'item' => url('projects/' . $work['slug'])],
            ],
        ],
    ],
];

require __DIR__ . '/app/parts/header.php';
?>

<article>
    <section class="container py-5">
        <nav class="small mb-4 reveal" aria-label="Breadcrumb">
            <a class="text-body-secondary" href="<?= url('projects') ?>">Projects</a>
            <span class="text-body-secondary mx-1">/</span>
            <span><?= e($work['title']) ?></span>
        </nav>

        <div class="row g-4 align-items-center">
            <div class="col-lg-8 reveal">
                <span class="work-icon mb-3 d-inline-flex"><?= icon($work['icon']) ?></span>
                <h1 class="display-4 mt-3 mb-3"><?= e($work['title']) ?></h1>
                <p class="lead mb-4"><?= e($work['summary']) ?></p>

                <div class="d-flex flex-wrap gap-2">
                    <?php if (!empty($work['repo'])): ?>
                        <a class="btn btn-primary" href="<?= e($work['repo']) ?>" target="_blank" rel="noopener">
                            <?= icon('fa-brands fa-github') ?> View the code
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($work['live'])): ?>
                        <a class="btn btn-ghost" href="<?= e($work['live']) ?>" target="_blank" rel="noopener">
                            <?= icon('fa-solid fa-arrow-up-right-from-square') ?> Open live
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="container pb-5">
        <div class="row g-4 g-lg-5">
            <div class="col-lg-8 reveal">
                <?= prose($work['body']) ?>
            </div>

            <div class="col-lg-4">
                <div class="card p-4 reveal" style="position: sticky; top: 6rem;">
                    <h2 class="h6 eyebrow mb-3">Project details</h2>
                    <ul class="list-unstyled d-grid gap-3 mb-0 small">
                        <li>
                            <div class="text-body-secondary">Category</div>
                            <div class="fw-semibold text-capitalize"><?= e($work['category']) ?></div>
                        </li>
                        <li>
                            <div class="text-body-secondary">Built with</div>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                <?php foreach ($tech as $one): ?>
                                    <span class="badge rounded-pill text-bg-secondary fw-normal"><?= e($one) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </li>
                        <li>
                            <div class="text-body-secondary">Added</div>
                            <div class="fw-semibold"><?= e(stamp($work['created'], 'j M Y')) ?></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</article>

<?php if ($others !== []): ?>
    <section class="container pb-5">
        <hr class="mb-5">
        <h2 class="h3 mb-4 reveal">More projects</h2>
        <div class="row g-4">
            <?php foreach ($others as $item): ?>
                <div class="col-md-6 col-lg-4 reveal">
                    <a class="card work h-100 text-decoration-none" href="<?= url('projects/' . $item['slug']) ?>">
                        <div class="card-body p-4 d-flex flex-column">
                            <span class="work-icon mb-3"><?= icon($item['icon']) ?></span>
                            <h3 class="h5"><?= e($item['title']) ?></h3>
                            <p class="text-body-secondary mb-0"><?= e(cut($item['summary'], 110)) ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/app/parts/footer.php'; ?>
