<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 validation aggregate loader다. include 선언만 유지한다.
// 가입, 인증, 계정, AJAX 검증 규칙은 validation-*.lib.php에 분리되어 있다.

require_once __DIR__ . '/runtime.lib.php';
require_once __DIR__ . '/validation-register.lib.php';
require_once __DIR__ . '/validation-auth.lib.php';
require_once __DIR__ . '/validation-account.lib.php';
require_once __DIR__ . '/validation-ajax.lib.php';
