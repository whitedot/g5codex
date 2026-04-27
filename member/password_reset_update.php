<?php
// 검증 지도: 비밀번호 재설정 저장 action controller다. 검증과 저장은 validation-auth, persist-auth, flow-auth-password에서 처리한다.
include_once('./_common.php');

$member_request_context = member_get_runtime_request_context();
$request = member_read_password_reset_request($member_request_context['post'], $member_request_context['session']);
member_complete_password_reset_request($request);
