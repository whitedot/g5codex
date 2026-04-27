<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원가입 flow aggregate loader다. include 선언만 유지한다.
// 가입 메일, 이메일 인증, submit 완료, form 상태 처리는 flow-register-*.lib.php에 분리되어 있다.

require_once __DIR__ . '/flow-register-notification.lib.php';
require_once __DIR__ . '/flow-register-email.lib.php';
require_once __DIR__ . '/flow-register-submit.lib.php';
require_once __DIR__ . '/flow-register-form.lib.php';
