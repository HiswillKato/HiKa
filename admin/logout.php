<?php
declare(strict_types=1);
require __DIR__ . '/../app/init.php';

$_SESSION = [];
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

redirect('admin/login');
