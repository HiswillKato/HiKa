<?php
declare(strict_types=1);
require __DIR__ . '/app/init.php';

/* =========================================================================
   SITEMAP
   Every address worth indexing, built from the rows the pages already read,
   so the file can never list a project that has been unpublished. Served at
   /sitemap.xml by a rule in .htaccess; the XML is assembled as a string so
   there is no stray whitespace before the declaration.
   ========================================================================= */

header('Content-Type: application/xml; charset=utf-8');

$pages = [
    ['',         '1.0', 'weekly'],
    ['about',    '0.8', 'monthly'],
    ['skills',   '0.8', 'monthly'],
    ['projects', '0.9', 'weekly'],
    ['contact',  '0.6', 'yearly'],
];

$urls = [];
foreach ($pages as [$path, $priority, $freq]) {
    $urls[] = ['loc' => url($path), 'freq' => $freq, 'priority' => $priority, 'when' => ''];
}
foreach (projects() as $item) {
    $urls[] = [
        'loc'      => url('projects/' . $item['slug']),
        'freq'     => 'monthly',
        'priority' => '0.7',
        'when'     => (string) ($item['created'] ?? ''),
    ];
}

$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
     . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($urls as $url) {
    $xml .= "  <url>\n"
          . '    <loc>' . e($url['loc']) . "</loc>\n";

    if ($url['when'] !== '') {
        $xml .= '    <lastmod>' . e(gmdate('Y-m-d', strtotime($url['when']))) . "</lastmod>\n";
    }

    $xml .= '    <changefreq>' . $url['freq'] . "</changefreq>\n"
          . '    <priority>' . $url['priority'] . "</priority>\n"
          . "  </url>\n";
}

echo $xml . "</urlset>\n";
