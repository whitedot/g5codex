<?php
// 검증 지도: 로그인 submit action controller다. 입력 정규화/검증/세션 생성은 member auth request/validation/persist/flow 파일에서 처리한다.
include_once('./_common.php');

$g5['title'] = "로그인 검사";

$member_request_context = member_get_runtime_request_context();
$request = member_read_login_request($member_request_context['post'], $member_request_context['query_state']);
member_complete_login_request($request, $member_view_path, $member_request_context['post']);
