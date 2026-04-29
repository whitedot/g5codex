<?php
// 검증 지도: 커뮤니티 포인트 관리 controller다.
$sub_menu = "600500";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$community_point_request = community_admin_read_point_list_request(g5_get_runtime_get_input(), $config);
$community_point_view = community_admin_build_point_list_view($community_point_request, $config);

admin_apply_page_view($community_point_view);
require_once './admin.head.php';
include_once G5_ADMIN_PATH . '/community_point_parts/list.php';
require_once './admin.tail.php';
