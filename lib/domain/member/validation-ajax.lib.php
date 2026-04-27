<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 AJAX 아이디/이메일/휴대폰/닉네임 중복 검사 규칙을 담당한다.
// 이 파일은 짧은 die 응답을 유지하며, 요청 정규화는 request-ajax.lib.php에서 처리한다.

function member_validate_ajax_mb_id(array $request)
{
    if ($msg = empty_mb_id($request['mb_id'])) die($msg);
    if ($msg = valid_mb_id($request['mb_id'])) die($msg);
    if ($msg = count_mb_id($request['mb_id'])) die($msg);
    if ($msg = exist_mb_id($request['mb_id'])) die($msg);
    if ($msg = reserve_mb_id($request['mb_id'])) die($msg);
}

function member_validate_ajax_mb_email(array $request)
{
    if ($msg = empty_mb_email($request['mb_email'])) die($msg);
    if ($msg = valid_mb_email($request['mb_email'])) die($msg);
    if ($msg = prohibit_mb_email($request['mb_email'])) die($msg);
    if ($msg = exist_mb_email($request['mb_email'], $request['mb_id'])) die($msg);
}

function member_validate_ajax_mb_hp(array $request)
{
    if ($msg = valid_mb_hp($request['mb_hp'])) die($msg);
}

function member_validate_ajax_mb_nick(array $request)
{
    if ($msg = empty_mb_nick($request['mb_nick'])) die($msg);
    if ($msg = valid_mb_nick($request['mb_nick'])) die($msg);
    if ($msg = count_mb_nick($request['mb_nick'])) die($msg);
    if ($msg = exist_mb_nick($request['mb_nick'], $request['mb_id'])) die($msg);
    if ($msg = reserve_mb_nick($request['mb_nick'])) die($msg);
}
