<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 flow aggregate loader다. include 선언만 유지한다.
// 로그인, 가입, 이메일 인증, 계정 처리, AJAX 완료 흐름은 flow-*.lib.php에 분리되어 있다.

require_once __DIR__ . '/runtime.lib.php';
require_once __DIR__ . '/flow-core.lib.php';
require_once __DIR__ . '/flow-auth.lib.php';
require_once __DIR__ . '/flow-register.lib.php';
require_once __DIR__ . '/flow-account.lib.php';
require_once __DIR__ . '/flow-ajax.lib.php';
