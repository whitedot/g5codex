<?php
// 검증 지도: 비밀번호 찾기 submit action controller다. 요청/검증/메일 발송은 auth request/validation/persist/flow 파일에서 처리한다.
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');
include_once(G5_LIB_PATH.'/support/mail.lib.php');

$member_request_context = member_get_runtime_request_context();
$request = member_read_password_lost_request($member_request_context['post']);
member_complete_password_lost_request($request, $is_member);
