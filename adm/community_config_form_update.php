<?php
// 검증 지도: 커뮤니티 기본환경 설정 저장 action controller다.
// 요청 정규화/저장은 lib/domain/community/admin-*.lib.php에서 처리한다.
$sub_menu = "600050";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$community_config_save_request = community_admin_read_config_save_request(g5_get_runtime_post_input());
$community_config_save_result = community_admin_save_config($community_config_save_request);

if ($community_config_save_result['error'] !== '') {
    alert($community_config_save_result['error'], './community_config_form.php');
}

admin_set_flash_message('success', '커뮤니티 기본환경 설정을 저장했습니다.');
goto_url('./community_config_form.php');
