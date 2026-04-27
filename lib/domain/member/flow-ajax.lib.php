<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 AJAX 중복 검사 완료 흐름과 session 반영을 담당한다.
// 요청 정규화와 검증 규칙은 request-ajax.lib.php 및 validation-ajax.lib.php에서 처리한다.

function member_reset_ajax_identity_checks(array $fields)
{
    foreach ($fields as $field) {
        set_session($field, '');
    }
}

function member_store_ajax_mb_id_check(array $request)
{
    set_session('ss_check_mb_id', $request['mb_id']);
}

function member_store_ajax_mb_email_check(array $request)
{
    set_session('ss_check_mb_email', $request['mb_email']);
}

function member_store_ajax_mb_nick_check(array $request)
{
    set_session('ss_check_mb_nick', $request['mb_nick']);
}

function member_process_ajax_mb_id(array $request)
{
    member_reset_ajax_identity_checks(array('ss_check_mb_id'));
    member_validate_ajax_mb_id($request);
    member_store_ajax_mb_id_check($request);
}

function member_process_ajax_mb_email(array $request)
{
    member_reset_ajax_identity_checks(array('ss_check_mb_email'));
    member_validate_ajax_mb_email($request);
    member_store_ajax_mb_email_check($request);
}

function member_process_ajax_mb_hp(array $request)
{
    member_validate_ajax_mb_hp($request);
}

function member_process_ajax_mb_nick(array $request)
{
    member_reset_ajax_identity_checks(array('ss_check_mb_nick'));
    member_validate_ajax_mb_nick($request);
    member_store_ajax_mb_nick_check($request);
}
