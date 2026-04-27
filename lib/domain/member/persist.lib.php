<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 persist aggregate loader다. include 선언만 유지한다.
// DB 조회/저장 책임은 persist-*.lib.php 세부 파일에 둔다.

require_once __DIR__ . '/runtime.lib.php';
require_once __DIR__ . '/persist-core.lib.php';
require_once __DIR__ . '/persist-register.lib.php';
require_once __DIR__ . '/persist-register-email.lib.php';
require_once __DIR__ . '/persist-auth.lib.php';
require_once __DIR__ . '/persist-account.lib.php';
