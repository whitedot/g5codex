<?php
// 검증 지도: 이메일 수신중지 action controller다. 요청/검증/저장/alert 종료는 auth/register-email domain 파일에서 처리한다.
include_once('./_common.php');

$member_request_context = member_get_runtime_request_context();
$request = member_read_email_stop_request($member_request_context['request']);
member_process_email_stop($request);
