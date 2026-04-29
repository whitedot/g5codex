<?php
$sub_menu = "400100";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$community_banner_save_request = community_admin_read_banner_save_request(g5_get_runtime_post_input());
$community_banner_save_result = community_admin_save_banner($community_banner_save_request, $_FILES);

if ($community_banner_save_result['error'] !== '') {
    alert($community_banner_save_result['error'], './site_banner_form.php' . ($community_banner_save_result['banner_id'] > 0 ? '?banner_id=' . (int) $community_banner_save_result['banner_id'] : ''));
}

admin_set_flash_message('success', '사이트 배너를 저장했습니다.');
goto_url('./site_banner_list.php');
