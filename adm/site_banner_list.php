<?php
$sub_menu = "400100";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$community_banner_request = community_admin_read_banner_list_request(g5_get_runtime_get_input(), $config);
$community_banner_list_view = community_admin_build_banner_list_view($community_banner_request, $config);

admin_apply_page_view($community_banner_list_view);
require_once './admin.head.php';
include_once G5_ADMIN_PATH . '/site_banner_parts/list.php';
require_once './admin.tail.php';
