<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 관리자 회원 form aggregate loader다. include 선언만 유지한다.
// 요청, 화면 배열, 검증, 저장, 완료 flow는 파일명 기준으로 분리되어 있다.
require_once __DIR__ . '/member-form-request.lib.php';
require_once __DIR__ . '/member-form-view.lib.php';
require_once __DIR__ . '/member-form-validation.lib.php';
require_once __DIR__ . '/member-form-persist.lib.php';
require_once __DIR__ . '/member-form-update.lib.php';
