<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 관리자 UI aggregate loader다. include 선언만 유지한다.
// legacy helper, anchor tab, shell view-model은 각각 ui-*.lib.php에서 담당한다.
require_once __DIR__ . '/ui-legacy.lib.php';
require_once __DIR__ . '/ui-anchor.lib.php';
require_once __DIR__ . '/ui-shell.lib.php';
