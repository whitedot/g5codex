<?php
// 검증 지도: 커뮤니티 게시판 저장 action controller다.
// 요청 정규화/검증/저장은 lib/domain/community/admin-*.lib.php에서 처리한다.
$sub_menu = "300100";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$community_board_save_request = community_admin_read_board_save_request(g5_get_runtime_post_input());
$community_board_save_result = community_admin_save_board($community_board_save_request);

if ($community_board_save_result['error'] !== '') {
    alert($community_board_save_result['error'], './community_board_form.php' . ($community_board_save_result['board_id'] !== '' ? '?board_id=' . rawurlencode($community_board_save_result['board_id']) : ''));
}

admin_set_flash_message('success', '커뮤니티 게시판을 저장했습니다.');
goto_url('./community_board_list.php');
