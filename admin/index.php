<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

$title = 'Dashboard';
guard();

$project_count   = total("SELECT COUNT(*) FROM projects WHERE status = 'published'");
$draft_count     = total("SELECT COUNT(*) FROM projects WHERE status = 'draft'");
$skill_count     = total('SELECT COUNT(*) FROM skills');
$social_count    = total('SELECT COUNT(*) FROM socials WHERE shown = 1');
$message_count   = total('SELECT COUNT(*) FROM messages');
$unread_count    = total('SELECT COUNT(*) FROM messages WHERE seen = 0');
$recent          = all('SELECT id, name, email, subject, seen, created FROM messages ORDER BY created DESC LIMIT 5');

$actions = '<a class="btn btn-primary btn-sm" href="' . panel('project') . '">'
    . icon('fa-solid fa-plus') . ' New project</a>';

require __DIR__ . '/parts/header.php';
?>

<div class="row g-3 mb-4">
    <?php
    $tiles = [
        ['fa-solid fa-diagram-project', $project_count, 'Published projects', 'projects'],
        ['fa-regular fa-file-lines',     $draft_count,   'Drafts',             'projects'],
        ['fa-solid fa-layer-group',      $skill_count,   'Skills listed',      'skills'],
        ['fa-solid fa-share-nodes',      $social_count,  'Social links',       'socials'],
        ['fa-solid fa-envelope',         $message_count, 'Messages',           'messages'],
        ['fa-regular fa-bell',           $unread_count,  'Unread',             'messages'],
    ];
    foreach ($tiles as [$glyph, $number, $text, $link]):
    ?>
        <div class="col-6 col-lg-2">
            <a class="card text-decoration-none h-100" href="<?= panel($link) ?>">
                <div class="card-body">
                    <span class="work-icon mb-2"><?= icon($glyph) ?></span>
                    <div class="h3 mb-0"><?= (int) $number ?></div>
                    <div class="small text-body-secondary"><?= e($text) ?></div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Recent messages</span>
                <a class="small" href="<?= panel('messages') ?>">See all <?= icon('fa-solid fa-arrow-right ms-1') ?></a>
            </div>
            <div class="card-body p-0">
                <?php if ($recent === []): ?>
                    <p class="text-body-secondary p-4 mb-0">No messages yet. When somebody writes, it lands here.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent as $item): ?>
                            <a class="list-group-item list-group-item-action d-flex gap-3 align-items-center"
                               href="<?= panel('message?id=' . $item['id']) ?>">
                                <span class="brand-mark" style="width:2.4rem;height:2.4rem;font-size:.85rem;">
                                    <?= e(initials($item['name'])) ?>
                                </span>
                                <span class="flex-grow-1 min-width-0">
                                    <span class="d-flex justify-content-between gap-2">
                                        <span class="fw-semibold text-truncate"><?= e($item['name']) ?></span>
                                        <span class="small text-body-secondary text-nowrap"><?= e(ago($item['created'])) ?></span>
                                    </span>
                                    <span class="small text-body-secondary d-block text-truncate">
                                        <?= e($item['subject'] !== '' ? $item['subject'] : $item['email']) ?>
                                    </span>
                                </span>
                                <?php if (!$item['seen']): ?>
                                    <span class="badge rounded-pill text-bg-warning">New</span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header fw-semibold">Quick actions</div>
            <div class="card-body d-grid gap-2">
                <a class="btn btn-primary" href="<?= panel('project') ?>">
                    <?= icon('fa-solid fa-plus') ?> Add a project
                </a>
                <a class="btn btn-ghost" href="<?= panel('skill') ?>">
                    <?= icon('fa-solid fa-plus') ?> Add a skill
                </a>
                <a class="btn btn-ghost" href="<?= panel('social') ?>">
                    <?= icon('fa-solid fa-plus') ?> Add a social link
                </a>
                <a class="btn btn-ghost" href="<?= panel('profile') ?>">
                    <?= icon('fa-solid fa-id-badge') ?> Edit my profile
                </a>
                <a class="btn btn-ghost" href="<?= url() ?>" target="_blank" rel="noopener">
                    <?= icon('fa-solid fa-arrow-up-right-from-square') ?> View the live site
                </a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/parts/footer.php'; ?>
