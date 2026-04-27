<?php
// 검증 지도: 회원 탈퇴 action controller다. 요청 정규화/검증/상태 변경은 account request/validation/flow 파일에서 처리한다.
include_once('./_common.php');

$member_request_context = member_get_runtime_request_context();
$request = member_read_leave_request($member_request_context['post'], $member_request_context['query_state']);
member_complete_leave_request($member, $is_admin, $request);
