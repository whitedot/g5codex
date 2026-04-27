<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 관리자 회원 list aggregate loader다. include 선언만 유지한다.
// 요청, 조회, 검증, 저장, 완료 flow, 화면 배열은 파일명 기준으로 분리되어 있다.
require_once __DIR__ . '/member-list-request.lib.php';
require_once __DIR__ . '/member-list-query.lib.php';
require_once __DIR__ . '/member-list-validation.lib.php';
require_once __DIR__ . '/member-list-persist.lib.php';
require_once __DIR__ . '/member-list-update.lib.php';
require_once __DIR__ . '/member-list-view.lib.php';
