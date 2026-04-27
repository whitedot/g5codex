<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// Member list aggregate loader: request parsing, bulk update/delete flows, then list view model.
require_once __DIR__ . '/member-list-request.lib.php';
require_once __DIR__ . '/member-list-query.lib.php';
require_once __DIR__ . '/member-list-validation.lib.php';
require_once __DIR__ . '/member-list-persist.lib.php';
require_once __DIR__ . '/member-list-update.lib.php';
require_once __DIR__ . '/member-list-view.lib.php';
