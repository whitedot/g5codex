<?php
// 검증 지도: 커뮤니티 게시글 일괄 처리 controller다.
$sub_menu = "600200";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$community_post_update_request = community_admin_read_post_list_update_request(g5_get_runtime_post_input());
$community_post_update_result = community_admin_apply_post_action($community_post_update_request);
$community_post_return_url = './community_post_list.php';
if ($community_post_update_request['return_query'] !== '') {
    $community_post_return_url .= '?' . $community_post_update_request['return_query'];
}

if ($community_post_update_result['error'] !== '') {
    alert($community_post_update_result['error'], $community_post_return_url);
}

admin_set_flash_message('success', '커뮤니티 게시글 ' . $community_post_update_result['count'] . '건을 처리했습니다.');
goto_url($community_post_return_url);
