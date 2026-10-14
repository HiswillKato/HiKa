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

/* The kinds a timeline entry can be, in the order the admin offers them. */
function journeys(): array
{
    return [
        'education' => 'Education',
        'work' => 'Work',
        'milestone' => 'Milestone',
    ];
}

/* =========================================================================
   SITE SWITCHES
   The on/off switches of the whole site. `flag()` reads the flags table and
   falls back to the defaults declared here, so the admin panel edits real
   state while a fresh install starts with the same site. Each value is
   [label shown in the admin panel, help text, the starting value].
   ========================================================================= */

function settings(): array
{
    return [
        'levels'      => ['Skill levels', 'Show the % and progress bars next to every skill.', true],
        'maintenance' => ['Maintenance mode', 'Visitors see a maintenance page; you can still sign in here.', false],
    ];
}

function flag(string $key): bool
{
    static $flags = null;

    if ($flags === null) {
        $flags = [];
        foreach (all('SELECT `key`, value FROM flags') as $row) {
            $flags[$row['key']] = (bool) $row['value'];
        }
    }

    return $flags[$key] ?? (settings()[$key][2] ?? false);
}

/* =========================================================================
   THE HERO TERMINAL
   One row in the terminal table drives everything the terminal prints — the
   host and folder for the title bar and prompts, and `lines`, one
   `kind::content` line per row of output:
     c::whoami            a command — prompt, folder and command colours
     out::Hello there     plain output; {name} and {title} become the profile
     type::A, B, C        a rotating typing line (comma separated items)
   ========================================================================= */

/* The single terminal row — the host, folder and the lines it prints. */
function terminal(): array
{
    static $term = null;

    if ($term === null) {
        $term = one('SELECT * FROM terminal ORDER BY id LIMIT 1') ?? [];
    }

    return $term;
}

/* Replace {placeholders} of a terminal line with live profile values. */
function fill(string $line): string
{
    $me = me();

    return str_replace(
        ['{name}', '{title}'],
        [$me['name'] ?? OWNER, $me['title'] ?? ''],
        $line
    );
}

/* =========================================================================
   THE HERO TERMINAL — RENDER
   ========================================================================= */

function termHtml(): string
{
    $term = terminal();
    $host = e($term['host'] ?? 'hiswill@localhost');
    $path = e($term['path'] ?? '~');
    $out  = '';

    foreach (preg_split('/\R/', trim($term['lines'] ?? '')) ?: [] as $line) {
        [$kind, $body] = array_pad(explode('::', $line, 2), 2, '');

        if ($kind === 'c') {
            [$command, $rest] = array_pad(explode(' ', $body, 2), 2, '');
            $out .= '<div' . ($out === '' ? '' : ' class="mt-3"') . '><span class="prompt">' . $host . '</span>:'
                . '<span class="path">' . $path . '</span>$ '
                . '<span class="arg">' . e($command) . '</span>'
                . ($rest === '' ? '' : ' ' . e($rest)) . '</div>';
        } elseif ($kind === 'out') {
            $out .= '<div class="out">' . e(fill($body)) . '</div>';
        } elseif ($kind === 'type') {
            $items = array_map('trim', explode(',', $body));
            $out .= '<div class="out">&gt; <span data-typing=\''
                . e(json_encode($items, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS))
                . '\'></span><span class="caret"></span></div>';
        }
    }

    return $out;
}
