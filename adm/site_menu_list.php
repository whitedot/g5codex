<?php
$sub_menu = "300100";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$community_menu_request = community_admin_read_menu_list_request(g5_get_runtime_get_input(), $config);
$community_menu_list_view = community_admin_build_menu_list_view($community_menu_request, $config);

admin_apply_page_view($community_menu_list_view);
require_once './admin.head.php';
include_once G5_ADMIN_PATH . '/site_menu_parts/list.php';
require_once './admin.tail.php';
