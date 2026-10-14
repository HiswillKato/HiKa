<?php
declare(strict_types=1);

/* =========================================================================
   1. TEXT AND ADDRESSES
   ========================================================================= */

/* The one escaping point. Anything printed into HTML goes through here, so a
   stray quote in a project title cannot break the page. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/* Build an absolute address. With BASE_URL empty the name is read from the
   request, so the site answers to `hika`, `hiswill` or a real domain. */
function url(string $path = ''): string
{
    if (BASE_URL !== '') {
        $base = rtrim(BASE_URL, '/');
    } else {
        $scheme = (($_SERVER['HTTPS'] ?? '') === 'on') ? 'https' : 'http';
        $base = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    }

    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function upload(string $path): string
{
    return url(UPLOAD_DIR . '/' . ltrim($path, '/'));
}

function panel(string $path = ''): string
{
    /* The trailing slash matters on the bare /admin: the folder is real, so
       Apache would otherwise 301 /admin to /admin/ before our rewrite runs. */
    return url('admin/' . ltrim($path, '/'));
}

/* A URL-safe version of a title, used for project addresses. */
function slug(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';

    return trim($text, '-');
}

/* The first paragraph of a longer piece, for cards and previews. */
function cut(string $text, int $length = 150): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($text)) ?? '');

    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return rtrim(mb_substr($text, 0, $length), " \t\n\r\0\x0B.,") . '…';
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $letters = array_map(static fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)), $parts);

    return implode('', array_slice($letters, 0, 2));
}

/* =========================================================================
   2. DATES
   ========================================================================= */

function stamp(?string $time, string $format = 'j M Y'): string
{
    return $time ? date($format, strtotime($time)) : '';
}

function ago(?string $time): string
{
    if (!$time) {
        return '';
    }

    $seconds = time() - (int) strtotime($time);

    if ($seconds < 60) {
        return 'just now';
    }

    foreach ([['year', 31536000], ['month', 2592000], ['day', 86400], ['hour', 3600], ['minute', 60]] as [$label, $span]) {
        if ($seconds >= $span) {
            $amount = (int) floor($seconds / $span);

            return $amount . ' ' . $label . ($amount > 1 ? 's' : '') . ' ago';
        }
    }

    return 'just now';
}

/* =========================================================================
   3. PRESENTATION HELPERS
   ========================================================================= */

function icon(string $classes): string
{
    return '<i class="' . e($classes) . '" aria-hidden="true"></i>';
}

/* The one button that flips the colour scheme. `$extra` carries per-placement
   classes, so the same control can live in the navbar bar and the nav menu. */
function themeToggle(string $extra = ''): string
{
    $extra = $extra === '' ? '' : ' ' . $extra;

    return '<button type="button" class="btn btn-ghost rounded-circle theme-toggle' . $extra
        . '" aria-label="Switch theme">'
        . icon('fa-solid fa-moon') . icon('fa-solid fa-sun') . '</button>';
}

/* Turn a block of plain text into safe HTML. Lines are read one at a time, so
   a heading, a list and a paragraph can sit side by side without needing a
   blank line between them: a `## ` line is a heading, a run of `- ` lines is a
   list, and anything else gathers into a paragraph. Pass $headings as false to
   keep a `## ` line as an ordinary paragraph — the about bio reads better flat.
   Everything is escaped first, so no formatting can smuggle in a tag. */
function prose(string $text, bool $headings = true): string
{
    $out   = '';
    $para  = [];
    $items = [];

    $flush = static function () use (&$out, &$para, &$items): void {
        if ($items !== []) {
            $out .= '<ul class="d-grid gap-2 mb-4">';
            foreach ($items as $one) {
                $out .= '<li>' . e($one) . '</li>';
            }
            $out .= '</ul>';
            $items = [];
        }

        if ($para !== []) {
            $out  .= '<p>' . nl2br(e(implode("\n", $para))) . '</p>';
            $para  = [];
        }
    };

    foreach (preg_split('/\n/', trim($text)) ?: [] as $line) {
        $line = rtrim($line);

        if (trim($line) === '') {
            $flush();
        } elseif (str_starts_with($line, '## ')) {
            $flush();
            $out .= $headings
                ? '<h2 class="h4 mt-5 mb-3">' . e(substr($line, 3)) . '</h2>'
                : '<p>' . e(substr($line, 3)) . '</p>';
        } elseif (str_starts_with($line, '- ')) {
            if ($para !== []) {
                $flush();
            }
            $items[] = ltrim(substr($line, 2));
        } else {
            if ($items !== []) {
                $flush();
            }
            $para[] = $line;
        }
    }

    $flush();

    return $out;
}

/* The <option> list for a select box. `$pairs` is value => label. */
function options(array $pairs, string $selected = ''): string
{
    $out = '';

    foreach ($pairs as $value => $label) {
        $picked = (string) $value === $selected ? ' selected' : '';
        $out .= '<option value="' . e((string) $value) . '"' . $picked . '>' . e($label) . '</option>';
    }

    return $out;
}

/* A single labelled field for the admin forms. The label and the control are
   handed in already built; this only supplies the frame. */
function field(string $label, string $control, string $help = ''): string
{
    $note = $help === '' ? '' : '<div class="form-text">' . e($help) . '</div>';

    return '<div class="mb-3"><label class="form-label fw-semibold">' . e($label) . '</label>'
        . $control . $note . '</div>';
}

/* =========================================================================
   4. SESSION, FLASH MESSAGES AND CSRF
   ========================================================================= */

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function notice(): string
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    if (!$flash) {
        return '';
    }

    return '<div class="alert alert-' . e($flash['type']) . ' alert-dismissible fade show shadow-sm" role="alert">'
        . e($flash['message'])
        . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
        . '</div>';
}

function token(): string
{
    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['token'];
}

function csrf(): string
{
    return '<input type="hidden" name="token" value="' . e(token()) . '">';
}

/* The first thing any POST handler does. On a mismatch it stops, so a form
   posted from another site can never write to the database. */
function check(): void
{
    $sent = $_POST['token'] ?? '';

    if (!is_string($sent) || !hash_equals(token(), $sent)) {
        http_response_code(403);
        exit('That form had expired. Go back, reload the page and try again.');
    }
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

/* =========================================================================
   5. SIGNING IN
   ========================================================================= */

function owner(): ?array
{
    return $_SESSION['user'] ?? null;
}

/* Read a value from a POST, trimmed and safe to store. */
function post(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;

    return is_string($value) ? trim($value) : $default;
}

/* Check the email and password against the users table. A success writes the
   session row; a failure only says no. */
function attempt(string $email, string $password): bool
{
    $user = one('SELECT id, name, email, password, role FROM users WHERE email = ? AND status = 1', [$email]);

    if (!$user || !password_verify($password, $user['password'])) {
        return false;
    }

    unset($user['password']);
    $_SESSION['user'] = $user;
    session_regenerate_id(true);
    run('UPDATE users SET login = NOW() WHERE id = ?', [$user['id']]);

    return true;
}

/* The gate in front of every admin page: no session, no page. */
function guard(): array
{
    $user = owner();

    if (!$user) {
        redirect('admin/login');
    }

    return $user;
}

/* =========================================================================
   6. UPLOADED IMAGES
   ========================================================================= */

/* Store one uploaded picture under assets/uploads/<folder>/ and hand back the
   path to save in the database (or '' when there was no file). The real MIME
   type is sniffed, the extension is re-checked, and the filename is ours, so
   nothing a visitor sends can land as a script. */
function store(array $file, string $folder): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return '';
    }

    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > UPLOAD_MAX) {
        return '';
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $type = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

    if (!isset($allowed[$type])) {
        return '';
    }

    $folder = preg_replace('/[^a-z0-9-]/', '', strtolower($folder)) ?: 'misc';
    $target = __DIR__ . '/../' . UPLOAD_DIR . '/' . $folder;

    if (!is_dir($target)) {
        mkdir($target, 0775, true);
    }

    $name = bin2hex(random_bytes(8)) . '.' . $allowed[$type];

    if (!move_uploaded_file($file['tmp_name'], $target . '/' . $name)) {
        return '';
    }

    return $folder . '/' . $name;
}

function remove(string $path): void
{
    if ($path === '') {
        return;
    }

    $file = __DIR__ . '/../' . UPLOAD_DIR . '/' . ltrim($path, '/');

    if (is_file($file)) {
        unlink($file);
    }
}
