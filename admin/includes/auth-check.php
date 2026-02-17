<?php
/**
 * auth-check.php — Include at the very top of every admin page.
 * Bootstraps config/db/functions/auth and enforces authentication.
 */
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once dirname(__DIR__, 2) . '/includes/db.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';

require_auth();
