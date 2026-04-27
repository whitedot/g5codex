<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 관리자 기본환경 설정 aggregate loader다. include 선언만 유지한다.
// 화면 배열은 config-view.lib.php, 저장 요청과 완료 flow는 config-update.lib.php에서 담당한다.
require_once __DIR__ . '/config-view.lib.php';
require_once __DIR__ . '/config-update.lib.php';
