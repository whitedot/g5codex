<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 관리자 보안 helper loader다. 토큰/권한/security request 파일만 로드한다.
// 화면별 실행 흐름은 controller/bootstrap/domain flow에 둔다.
require_once __DIR__ . '/auth.lib.php';
require_once __DIR__ . '/token.lib.php';
require_once __DIR__ . '/request-security.lib.php';
