<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원가입 이메일 인증과 이메일 변경 인증 완료 흐름을 담당한다.
// 이메일 관련 DB 조회/저장은 persist-register-email.lib.php에서 처리한다.

function member_process_email_certify(array $request)
{
    $member_row = member_find_email_certify_candidate($request['mb_id']);
    member_validate_email_certify_member($member_row);

    member_clear_email_certify_token($request['mb_id']);

    $message = member_validate_email_certify_hash($request['mb_md5'], $member_row['mb_email_certify2'], $request['mb_id']);
    member_mark_email_certified($request['mb_id']);

    return $message;
}

function member_complete_email_certify_request(array $request)
{
    member_guard_mail_link_bot();
    $message = member_process_email_certify($request);

    alert($message, G5_URL);
}

function member_prepare_register_email_page(array $request)
{
    $mb = member_find_register_email_member($request['mb_id']);
    member_validate_register_email_member($mb);

    $key = g5_build_register_email_key($mb['mb_ip'], $mb['mb_datetime']);
    member_validate_register_email_key($request['ckey'], $key);

    return $mb;
}

function member_resend_register_email_change(array $request)
{
    $mb = member_find_pending_register_email_member($request['mb_id']);
    member_validate_register_email_pending_member($mb);
    member_validate_register_email_update_captcha();

    $count = member_count_other_members_by_email($request['mb_id'], $request['mb_email']);
    member_validate_register_email_uniqueness($count, $request['mb_email']);

    MemberNotificationService::sendRegisterEmailChange($request['mb_id'], $mb['mb_name'], $request['mb_email'], 'u');
    member_update_email_address($request['mb_id'], $request['mb_email']);
}
