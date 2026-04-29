<?php
$sub_menu = "300400";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$community_menu_save_request = community_admin_read_menu_save_request(g5_get_runtime_post_input());
$community_menu_save_result = community_admin_save_menu($community_menu_save_request);

if ($community_menu_save_result['error'] !== '') {
    alert($community_menu_save_result['error'], './community_menu_form.php' . ($community_menu_save_result['menu_id'] > 0 ? '?menu_id=' . (int) $community_menu_save_result['menu_id'] : ''));
}

admin_set_flash_message('success', '커뮤니티 메뉴를 저장했습니다.');
goto_url('./community_menu_list.php');
