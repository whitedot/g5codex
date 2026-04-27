<?php
// 검증 지도: 로그아웃 action controller다. 요청 정규화와 session/cookie 정리는 request-auth 및 flow-auth 파일에서 처리한다.
define('G5_CERT_IN_PROG', true);
include_once('./_common.php');

$member_request_context = member_get_runtime_request_context();
$request = member_read_logout_request($member_request_context['query_state']);
member_finalize_logout($request);
