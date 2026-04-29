<?php
$sub_menu = "300075";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$community_group_form_request = community_admin_read_group_form_request(g5_get_runtime_get_input());
$community_group_form_view = community_admin_build_group_form_view($community_group_form_request);

admin_apply_page_view($community_group_form_view);
require_once './admin.head.php';
include_once G5_ADMIN_PATH . '/community_group_parts/form.php';
require_once './admin.tail.php';
