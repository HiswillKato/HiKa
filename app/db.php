<?php
declare(strict_types=1);

/* =========================================================================
   DATABASE
   One connection, opened the first time somebody asks for it, and a small
   set of readers built on prepared statements. Every query in the site goes
   through `query()`, so no page has to remember how to bind parameters.
   ========================================================================= */

function db(): mysqli
{
    static $conn = null;

    if ($conn === null) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $conn->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $problem) {
            http_response_code(503);
            $detail = SHOW_ERRORS ? '<p><code>' . htmlspecialchars($problem->getMessage()) . '</code></p>' : '';
            exit('<!doctype html><meta charset="utf-8"><title>Database unavailable</title>'
                . '<div style="font:16px/1.6 system-ui;max-width:36rem;margin:14vh auto;padding:0 1.5rem">'
                . '<h1 style="font-size:1.4rem">The database is not reachable.</h1>'
                . $detail
                . '<p>Check the <code>DB_*</code> values in <code>app/config.php</code> and that MariaDB is running.</p>'
                . '</div>');
        }
    }

    return $conn;
}

/* Prepare, bind and run one statement. The type of each value is worked out
   from the value itself, so a caller never writes 'si' by hand. Arguments
   are bound by reference — which bind_param insists on — so they are copied
   into an array the call can point at. */
function query(string $sql, array $args = []): mysqli_stmt
{
    $statement = db()->prepare($sql);

    if ($args !== []) {
        $types = '';
        foreach ($args as $value) {
            $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
        }

        $bound = [$types];
        foreach ($args as $index => $ignored) {
            $bound[] = &$args[$index];
        }
        call_user_func_array([$statement, 'bind_param'], $bound);
    }

    $statement->execute();

    return $statement;
}

/* The readers. Named after what they hand back, so a page reads like a
   sentence: `one()`, `all()`, `value()`, `total()`. */
function one(string $sql, array $args = []): ?array
{
    $row = query($sql, $args)->get_result()->fetch_assoc();

    return $row ?: null;
}

function all(string $sql, array $args = []): array
{
    return query($sql, $args)->get_result()->fetch_all(MYSQLI_ASSOC);
}

function value(string $sql, array $args = []): mixed
{
    $row = query($sql, $args)->get_result()->fetch_row();

    return $row ? $row[0] : null;
}

function total(string $sql, array $args = []): int
{
    return (int) value($sql, $args);
}

/* A write with nothing to hand back: an UPDATE or a DELETE. */
function run(string $sql, array $args = []): void
{
    query($sql, $args);
}

/* A write whose new id you want: an INSERT. */
function add(string $sql, array $args = []): int
{
    query($sql, $args);

    return db()->insert_id;
}
