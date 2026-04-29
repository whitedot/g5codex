<?php
$sub_menu = "500100";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$site_page_save_request = site_admin_read_page_save_request(g5_get_runtime_post_input());
$site_page_save_result = site_admin_save_page($site_page_save_request);

if ($site_page_save_result['error'] !== '') {
    alert($site_page_save_result['error'], './site_page_form.php' . ($site_page_save_result['page_id'] > 0 ? '?page_id=' . (int) $site_page_save_result['page_id'] : ''));
}

admin_set_flash_message('success', '페이지를 저장했습니다.');
goto_url('./site_page_list.php');
