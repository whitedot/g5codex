<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 인증 flow aggregate loader다. include 선언만 유지한다.
// 알림, 비밀번호, 로그인 session 처리는 flow-auth-*.lib.php에서 담당한다.

require_once __DIR__ . '/flow-auth-notification.lib.php';
require_once __DIR__ . '/flow-auth-password.lib.php';
require_once __DIR__ . '/flow-auth-login.lib.php';
