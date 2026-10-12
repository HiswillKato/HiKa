<?php
declare(strict_types=1);

/* =========================================================================
   CONFIGURATION
   The handful of values that change from one machine to another. Nothing
   here is clever; it is only constants, so a wrong value is easy to spot.
   ========================================================================= */

/* The database. `hika` lives on the local MariaDB under the root account. */
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = 'root';
const DB_NAME = 'hika';

/* Who the site belongs to. Used in the <title> fallback and the footer. */
const OWNER = 'Hiswill Kato';
const ALIAS = 'Hika';
const EMAIL = 'hiswillkato@gmail.com';

/* Leave empty on the live domain and the address is read from the request,
   so the same code works under `hika`, `hiswill` or a real domain without
   an edit. Set it only when the site sits under a sub-folder. */
const BASE_URL = '';

/* Uploaded photographs land here. The database stores only the part after
   this folder, so the folder can move without touching a single row. */
const UPLOAD_DIR = 'assets/uploads';
const UPLOAD_MAX = 3 * 1024 * 1024; // 3 MB

/* Show the real error text while building; turn this to false before the
   site goes live so visitors never see a stack trace. */
const SHOW_ERRORS = true;

/* The length of a password the admin panel will accept. */
const MIN_PASSWORD = 8;

date_default_timezone_set('Africa/Kampala');
