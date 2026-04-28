<?php
// 검증 지도: 커뮤니티 게시판 목록 화면 controller다.
// 요청 정규화/조회/화면 배열은 lib/domain/community/admin-*.lib.php에서 처리한다.
$sub_menu = "300100";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$community_board_request = community_admin_read_board_list_request(g5_get_runtime_get_input(), $config);
$community_board_list_view = community_admin_build_board_list_view($community_board_request, $config);

admin_apply_page_view($community_board_list_view);
require_once './admin.head.php';
include_once G5_ADMIN_PATH . '/community_board_parts/list.php';
require_once './admin.tail.php';
