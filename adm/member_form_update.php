<?php
// 검증 지도: 이 action controller는 관리자 회원 등록/수정 저장 요청만 완료 함수로 넘긴다.
// 요청 정규화는 member-form-request.lib.php, action 검증은 member-form-validation.lib.php,
// DB 저장은 member-form-persist.lib.php, redirect/event 순서는 member-form-update.lib.php를 확인한다.
$sub_menu = "200100";
require_once "./_common.php";

$update_request = admin_read_member_form_update_request(g5_get_runtime_post_input(), $config);
admin_complete_member_form_update_request($update_request, $member, $is_admin, $auth, $sub_menu);
