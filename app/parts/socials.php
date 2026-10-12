<?php
/* One chip per social link. A link with an address opens it; a network with
   no public profile is a button that copies the handle instead, so there is
   never a chip that leads nowhere. */
$socials = socials();
?>
<div class="d-flex flex-wrap gap-2">
    <?php foreach ($socials as $item): ?>
        <?php if ($item['url'] !== ''): ?>
            <a class="social" href="<?= e($item['url']) ?>" target="_blank" rel="noopener"
               style="--brand: <?= e($item['color'] ?: 'var(--bs-primary)') ?>"
               title="<?= e($item['name'] . ' — ' . $item['handle']) ?>"
               aria-label="<?= e($item['name']) ?>">
                <?= icon($item['icon']) ?>
            </a>
        <?php else: ?>
            <button class="social" type="button"
                    data-copy="<?= e($item['handle']) ?>"
                    style="--brand: <?= e($item['color'] ?: 'var(--bs-primary)') ?>"
                    title="Copy <?= e($item['name'] . ' handle') ?>"
                    aria-label="Copy <?= e($item['name']) ?> handle">
                <?= icon($item['icon']) ?>
            </button>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
