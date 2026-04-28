<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원가입/정보수정 form 진입 전 session 상태와 진행 상태 초기화를 담당한다.
// 화면 view-model은 render-register-form.lib.php에서 조립한다.

function member_reset_registration_progress()
{
    set_session('ss_mb_reg', '');
}

function member_prepare_register_form_entry()
{
    run_event('register_form_before');

    set_session('ss_token', _token());
    set_session('ss_cert_no', '');
    set_session('ss_cert_hash', '');
    set_session('ss_cert_type', '');
}

function member_prepare_register_form_create_state(array $member, array $request)
{
    $member['mb_birth'] = $request['birth'];
    $member['mb_sex'] = $request['sex'];
    $member['mb_name'] = $request['mb_name'];

    return array(
        'title' => '회원 가입',
        'member' => $member,
    );
}

function member_prepare_register_form_update_state(array $member)
{
    set_session('ss_reg_mb_name', $member['mb_name']);
    set_session('ss_reg_mb_hp', $member['mb_hp']);

    $member['mb_email'] = get_text($member['mb_email']);
    $member['mb_birth'] = get_text($member['mb_birth']);
    $member['mb_hp'] = get_text($member['mb_hp']);

    return array(
        'title' => '회원 정보 수정',
        'member' => $member,
    );
}

function member_apply_register_form_defaults(array $member)
{
    $defaults = array(
        'mb_marketing_agree' => '0',
        'mb_marketing_date' => '0000-00-00 00:00:00',
        'mb_mailling' => '0',
        'mb_mailling_date' => '0000-00-00 00:00:00',
    );

    foreach ($defaults as $member_key => $default_value) {
        if (!isset($member[$member_key])) {
            $member[$member_key] = $default_value;
        }
    }

    return $member;
}
