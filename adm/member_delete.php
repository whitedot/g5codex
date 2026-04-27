<?php
// 검증 지도: 관리자 회원 삭제 action controller다.
// 요청 정규화는 member-form-request.lib.php, 삭제 검증/실행 순서는 member-form-update.lib.php를 확인한다.
// 이 파일에는 삭제 SQL이나 redirect 분기를 직접 추가하지 않는다.
$sub_menu = "200100";
require_once "./_common.php";

$delete_action_request = admin_read_member_delete_action_request(g5_get_runtime_post_input(), $config);
admin_complete_member_delete_request($delete_action_request, $member, $auth, $sub_menu);
