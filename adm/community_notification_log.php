<?php
// 검증 지도: 커뮤니티 메일 알림 로그 controller다.
$sub_menu = "300400";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$community_notification_request = community_admin_read_notification_log_request(g5_get_runtime_get_input(), $config);
$community_notification_view = community_admin_build_notification_log_view($community_notification_request, $config);

admin_apply_page_view($community_notification_view);
require_once './admin.head.php';
include_once G5_ADMIN_PATH . '/community_notification_parts/list.php';
require_once './admin.tail.php';
