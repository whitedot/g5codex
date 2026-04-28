<?php
// 검증 지도: 커뮤니티 운영 점검 화면 controller다.
$sub_menu = "300600";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$community_health_view = community_admin_build_health_view();

admin_apply_page_view($community_health_view);
require_once './admin.head.php';
include_once G5_ADMIN_PATH . '/community_health_parts/list.php';
require_once './admin.tail.php';
