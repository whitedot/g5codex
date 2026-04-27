<?php
// 검증 지도: 관리자 기본환경 설정 저장 action controller다.
// 요청 정규화와 저장 순서는 config-update.lib.php에서 담당한다.
// 이 파일은 demo 체크 후 완료 함수로 위임하는 역할만 유지한다.
$sub_menu = "100100";
require_once './_common.php';

check_demo();

$ori_config = admin_read_config_row();
$config_form_request = admin_read_config_form_update_request(g5_get_runtime_post_input(), $ori_config);
admin_complete_config_form_update_request($config_form_request, $ori_config, $auth, $sub_menu, $is_admin);
