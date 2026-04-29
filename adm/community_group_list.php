<?php
$sub_menu = "300075";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$community_group_request = community_admin_read_group_list_request(g5_get_runtime_get_input(), $config);
$community_group_list_view = community_admin_build_group_list_view($community_group_request, $config);

admin_apply_page_view($community_group_list_view);
require_once './admin.head.php';
include_once G5_ADMIN_PATH . '/community_group_parts/list.php';
require_once './admin.tail.php';
