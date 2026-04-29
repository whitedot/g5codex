<?php
$sub_menu = "500100";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$site_page_request = site_admin_read_page_list_request(g5_get_runtime_get_input(), $config);
$site_page_list_view = site_admin_build_page_list_view($site_page_request, $config);

admin_apply_page_view($site_page_list_view);
require_once './admin.head.php';
include_once G5_ADMIN_PATH . '/site_page_parts/list.php';
require_once './admin.tail.php';
