<?php
// 검증 지도: 커뮤니티 포인트 만료 정산 controller다.
$sub_menu = "600500";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$community_point_expire_request = community_admin_read_point_expire_request(g5_get_runtime_post_input());
$community_point_expire_result = community_admin_expire_points($community_point_expire_request);
$community_point_return_url = './community_point_list.php';
if ($community_point_expire_request['return_query'] !== '') {
    $community_point_return_url .= '?' . $community_point_expire_request['return_query'];
}

if ($community_point_expire_result['error'] !== '') {
    alert($community_point_expire_result['error'], $community_point_return_url);
}

$community_point_expire_message = '만료 포인트 ' . number_format($community_point_expire_result['expired_amount']) . '점을 ' . number_format($community_point_expire_result['expired_count']) . '건 정산했습니다.';
if (!empty($community_point_expire_result['has_more'])) {
    $community_point_expire_message .= ' 남은 만료 포인트가 있으면 다시 정산하세요.';
}

admin_set_flash_message('success', $community_point_expire_message);
goto_url($community_point_return_url);
