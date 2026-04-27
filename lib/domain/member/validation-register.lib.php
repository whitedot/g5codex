<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원가입/정보수정 검증 aggregate loader다. include 선언만 유지한다.
// 화면 진입 검증, submit 검증, 이메일 변경 검증은 세부 validation-register-*.lib.php에서 처리한다.

require_once __DIR__ . '/validation-register-submit.lib.php';
require_once __DIR__ . '/validation-register-email.lib.php';
require_once __DIR__ . '/validation-register-page.lib.php';
