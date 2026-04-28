<?php
// 검증 지도: 커뮤니티 알림 로그 일괄 처리 controller다.
$sub_menu = "300400";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$community_notification_update_request = community_admin_read_notification_log_update_request(g5_get_runtime_post_input());
$community_notification_update_result = community_admin_apply_notification_action($community_notification_update_request);
$community_notification_return_url = './community_notification_log.php';
if ($community_notification_update_request['return_query'] !== '') {
    $community_notification_return_url .= '?' . $community_notification_update_request['return_query'];
}

if ($community_notification_update_result['error'] !== '') {
    alert($community_notification_update_result['error'], $community_notification_return_url);
}

admin_set_flash_message('success', '커뮤니티 알림 ' . $community_notification_update_result['count'] . '건을 재발송 요청했습니다.');
goto_url($community_notification_return_url);
