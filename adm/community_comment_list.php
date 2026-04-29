<?php
// 검증 지도: 커뮤니티 댓글 관리 controller다.
$sub_menu = "600300";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$community_comment_request = community_admin_read_comment_list_request(g5_get_runtime_get_input(), $config);
$community_comment_view = community_admin_build_comment_list_view($community_comment_request, $config);

admin_apply_page_view($community_comment_view);
require_once './admin.head.php';
include_once G5_ADMIN_PATH . '/community_comment_parts/list.php';
require_once './admin.tail.php';
