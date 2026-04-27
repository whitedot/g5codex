<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 관리자 공통 부트스트랩 파일이다.
// 실행 시 접근 권한, 메뉴, request alias를 준비한다. 개별 화면 업무 로직은 domain helper 파일에 둔다.
require_once __DIR__ . '/access-bootstrap.lib.php';
require_once __DIR__ . '/menu-bootstrap.lib.php';
require_once __DIR__ . '/request-bootstrap.lib.php';

$auth = admin_bootstrap_require_access($member, $is_admin);
admin_bootstrap_verify_client($member);

$menu_state = admin_bootstrap_load_menus();
$amenu = $menu_state['amenu'];
$menu = $menu_state['menu'];
$admin_request_context = g5_get_runtime_request_context();
$admin_bootstrap_request = isset($admin_request_context['request']) && is_array($admin_request_context['request'])
    ? $admin_request_context['request']
    : array();
$admin_bootstrap_query_state = isset($admin_request_context['query_state']) && is_array($admin_request_context['query_state'])
    ? $admin_request_context['query_state']
    : array();
$qstr = admin_bootstrap_build_qstr($admin_bootstrap_query_state);

if ($admin_bootstrap_request) {
    admin_bootstrap_validate_request($admin_bootstrap_request);
}
