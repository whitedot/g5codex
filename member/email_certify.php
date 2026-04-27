<?php
// 검증 지도: 이메일 인증 링크 처리 controller다.
// 요청 정규화는 request-auth.lib.php, 토큰/회원 상태 검증은 validation-auth.lib.php,
// 인증 상태 저장은 persist-register-email.lib.php, alert 종료 흐름은 flow-auth.lib.php를 확인한다.
include_once('./_common.php');

$member_request_context = member_get_runtime_request_context();
$request = member_read_email_certify_request($member_request_context['request']);
member_complete_email_certify_request($request);
