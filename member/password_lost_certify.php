<?php
// 검증 지도: 비밀번호 찾기 인증 링크 action controller다. 요청 검증과 임시 비밀번호 flow는 auth domain 파일에서 처리한다.
include_once('./_common.php');

$member_request_context = member_get_runtime_request_context();
$request = member_read_password_lost_certify_request($member_request_context['request']);
member_complete_password_lost_certify_request($request);
