<?php
// 검증 지도: 커뮤니티 댓글 일괄 처리 controller다.
$sub_menu = "300300";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$community_comment_update_request = community_admin_read_comment_list_update_request(g5_get_runtime_post_input());
$community_comment_update_result = community_admin_apply_comment_action($community_comment_update_request);
$community_comment_return_url = './community_comment_list.php';
if ($community_comment_update_request['return_query'] !== '') {
    $community_comment_return_url .= '?' . $community_comment_update_request['return_query'];
}

if ($community_comment_update_result['error'] !== '') {
    alert($community_comment_update_result['error'], $community_comment_return_url);
}

admin_set_flash_message('success', '커뮤니티 댓글 ' . $community_comment_update_result['count'] . '건을 처리했습니다.');
goto_url($community_comment_return_url);
