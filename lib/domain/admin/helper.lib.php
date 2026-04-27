<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 관리자 domain 전체 loader다. include 선언만 유지하고 업무 로직을 넣지 않는다.
// 실행 시 권한/메뉴 준비는 bootstrap.lib.php, 화면/저장/export 세부 책임은 각 하위 loader에서 확인한다.
require_once __DIR__ . '/ui.lib.php';
require_once __DIR__ . '/view-helper.lib.php';
require_once __DIR__ . '/member.lib.php';
require_once __DIR__ . '/config.lib.php';
require_once __DIR__ . '/export.lib.php';
require_once __DIR__ . '/xlsx.lib.php';
