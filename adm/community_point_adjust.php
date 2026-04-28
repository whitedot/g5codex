<?php
// 검증 지도: 커뮤니티 포인트 수동 조정 controller다.
$sub_menu = "300500";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$community_point_adjust_request = community_admin_read_point_adjust_request(g5_get_runtime_post_input());
$community_point_adjust_result = community_admin_adjust_point($community_point_adjust_request, $member);
$community_point_return_url = './community_point_list.php';
if ($community_point_adjust_request['return_query'] !== '') {
    $community_point_return_url .= '?' . $community_point_adjust_request['return_query'];
}

if ($community_point_adjust_result['error'] !== '') {
    alert($community_point_adjust_result['error'], $community_point_return_url);
}

admin_set_flash_message('success', '커뮤니티 포인트를 조정했습니다.');
goto_url($community_point_return_url);
