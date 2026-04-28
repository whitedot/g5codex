<?php
// 검증 지도: 커뮤니티 게시글 관리 controller다.
$sub_menu = "300200";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$community_post_request = community_admin_read_post_list_request(g5_get_runtime_get_input(), $config);
$community_post_view = community_admin_build_post_list_view($community_post_request, $config);

admin_apply_page_view($community_post_view);
require_once './admin.head.php';
include_once G5_ADMIN_PATH . '/community_post_parts/list.php';
require_once './admin.tail.php';
