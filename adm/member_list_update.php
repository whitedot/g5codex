<?php
// 검증 지도: 이 action controller는 회원 목록의 선택수정/선택삭제 요청만 완료 함수로 넘긴다.
// 요청 정규화는 member-list-request.lib.php, 권한/토큰/행 검증은 member-list-validation.lib.php,
// DB update payload와 저장은 member-list-persist.lib.php, 업무 순서는 member-list-update.lib.php를 확인한다.
$sub_menu = "200100";
require_once './_common.php';

check_demo();

$admin_post_input = g5_get_runtime_post_input();
$member_list_qstr = admin_build_member_list_qstr($admin_post_input, $config);
$request = admin_read_member_list_update_request($admin_post_input);
admin_complete_member_list_update_request($request, $member, $is_admin, $auth, $sub_menu, $member_list_qstr);
