<?php
declare(strict_types=1);

/* =========================================================================
   DATA
   Every read the pages share, in one place. A page asks `me()` or `socials()`
   and never writes the SQL itself, so the shape of a row is decided here once.
   ========================================================================= */

/* The one profile row. Cached for the request, because every page needs it. */
function me(): array
{
    static $me = null;

    if ($me === null) {
        $me = one('SELECT * FROM profile ORDER BY id LIMIT 1') ?? [];
    }

    return $me;
}

function socials(): array
{
    return all('SELECT * FROM socials WHERE shown = 1 ORDER BY sort, id');
}

function skills(): array
{
    return all('SELECT * FROM skills WHERE shown = 1 ORDER BY sort, id');
}

/* Only the rows in one category — languages, stack, interests, practice. */
function pick(array $rows, string $category): array
{
    return array_values(array_filter($rows, static fn (array $row): bool => $row['category'] === $category));
}

function projects(bool $everything = false): array
{
    $clause = $everything ? '' : "WHERE status = 'published'";

    return all("SELECT * FROM projects $clause ORDER BY featured DESC, sort, created DESC");
}

function featured(int $limit = 3): array
{
    return all("SELECT * FROM projects WHERE status = 'published' AND featured = 1 ORDER BY sort, created DESC LIMIT ?", [$limit]);
}

function project(string $slug): ?array
{
    return one("SELECT * FROM projects WHERE slug = ? AND status = 'published'", [$slug]);
}

function timeline(): array
{
    return all('SELECT * FROM timeline WHERE shown = 1 ORDER BY sort, id');
}

/* The icon names the admin may choose from, so a social link always has a
   picture. Kept here rather than in markup so a name changes in one place. */
function platforms(): array
{
    return [
        'GitHub' => 'fa-brands fa-github',
        'GitLab' => 'fa-brands fa-gitlab',
        'Bitbucket' => 'fa-brands fa-bitbucket',
        'X (Twitter)' => 'fa-brands fa-x-twitter',
        'Facebook' => 'fa-brands fa-facebook-f',
        'Instagram' => 'fa-brands fa-instagram',
        'LinkedIn' => 'fa-brands fa-linkedin-in',
        'YouTube' => 'fa-brands fa-youtube',
        'WhatsApp' => 'fa-brands fa-whatsapp',
        'Telegram' => 'fa-brands fa-telegram',
        'Reddit' => 'fa-brands fa-reddit-alien',
        'TikTok' => 'fa-brands fa-tiktok',
        'Mastodon' => 'fa-brands fa-mastodon',
        'Dev.to' => 'fa-brands fa-dev',
        'Stack Overflow' => 'fa-brands fa-stack-overflow',
        'Email' => 'fa-solid fa-envelope',
        'Website' => 'fa-solid fa-globe',
        'Phone' => 'fa-solid fa-phone',
    ];
}

/* The groups on the skills page, in the order they are shown. */
function categories(): array
{
    return [
        'language' => 'Languages',
        'stack' => 'Tools & Platforms',
        'interest' => 'Interests',
        'practice' => 'Hands-on Skills',
    ];
}
