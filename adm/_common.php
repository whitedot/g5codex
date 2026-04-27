<?php
// 관리자 영역 공통 진입 wrapper다. 루트 common.php 로드 후 admin.lib.php를 통해 domain bootstrap/helper를 준비한다.
define('G5_IS_ADMIN', true);
require_once '../common.php';
require_once G5_ADMIN_PATH . '/admin.lib.php';

if (!empty($g5['request_context']['token'])) {
    $token = admin_sanitize_token_value($g5['request_context']['token']);
}

run_event('admin_common');
