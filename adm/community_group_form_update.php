<?php
$sub_menu = "300075";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$community_group_save_request = community_admin_read_group_save_request(g5_get_runtime_post_input());
$community_group_save_result = community_admin_save_group($community_group_save_request);

if ($community_group_save_result['error'] !== '') {
    alert($community_group_save_result['error'], './community_group_form.php' . ($community_group_save_result['group_id'] !== '' ? '?group_id=' . rawurlencode($community_group_save_result['group_id']) : ''));
}

admin_set_flash_message('success', '게시판 그룹을 저장했습니다.');
goto_url('./community_group_list.php');
