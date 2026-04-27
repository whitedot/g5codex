<?php
// 검증 지도: 이 action controller는 회원가입/정보수정 저장 요청만 도메인 흐름으로 넘긴다.
// 요청 정규화는 request-register.lib.php, 입력 검증은 validation-register*.lib.php,
// DB 저장은 persist-register*.lib.php, 메일/redirect 흐름은 flow-register*.lib.php를 확인한다.
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');
include_once(G5_LIB_PATH.'/register.lib.php');
include_once(G5_LIB_PATH.'/support/mail.lib.php');

$member_request_context = member_get_runtime_request_context();
$register_request = member_read_registration_request($member_request_context['post'], $member_request_context['session'], $member_request_context['query_state']);
member_complete_register_submit_request($register_request['w'], $register_request, $member, $config, $is_admin, $member_view_path);
