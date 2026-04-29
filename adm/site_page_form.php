<?php
$sub_menu = "500100";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$site_page_form_request = site_admin_read_page_form_request(g5_get_runtime_get_input());
$site_page_form_view = site_admin_build_page_form_view($site_page_form_request);

admin_apply_page_view($site_page_form_view);
require_once './admin.head.php';
include_once G5_ADMIN_PATH . '/site_page_parts/form.php';
require_once './admin.tail.php';
