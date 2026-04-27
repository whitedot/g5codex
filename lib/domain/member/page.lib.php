<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 page aggregate loader다. include 선언만 유지한다.
// page controller, hook, shell include helper는 page-*.lib.php에서 담당한다.

require_once __DIR__ . '/page-controller.lib.php';
require_once __DIR__ . '/page-hook.lib.php';
require_once __DIR__ . '/page-shell.lib.php';
