<?php
// 검증 지도: 회원 본인확인 갱신 저장 action controller다. 요청/검증/상태 변경은 account request/validation/flow 파일에서 처리한다.
define('G5_CERT_IN_PROG', true);
include_once('./_common.php');

$member_request_context = member_get_runtime_request_context();
$request = member_read_cert_refresh_request($member_request_context['post'], $member_request_context['query_state']);
member_complete_cert_refresh_update_request($request, $is_member, $member, $config);
