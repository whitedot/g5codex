<?php
// 검증 지도: 커뮤니티 게시판 form controller다.
// 화면 배열은 community admin render 파일, 저장은 community_board_form_update.php에서 처리한다.
$sub_menu = "600100";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$community_board_form_request = community_admin_read_board_form_request(g5_get_runtime_get_input());
$community_board_form_view = community_admin_build_board_form_view($community_board_form_request);

admin_apply_page_view($community_board_form_view);
require_once './admin.head.php';
include_once G5_ADMIN_PATH . '/community_board_parts/form.php';
require_once './admin.tail.php';
