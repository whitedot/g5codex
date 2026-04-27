<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 로그인/비밀번호 찾기/재설정에 필요한 회원 조회와 저장을 담당한다.
// 이메일 인증 상태 변경은 persist-register-email.lib.php에서 처리한다.

function member_find_password_lost_candidate($email)
{
    $member_table = member_get_member_table_name();

    return sql_fetch_prepared(
        " select mb_no, mb_id, mb_name, mb_nick, mb_email, mb_datetime, mb_leave_date from {$member_table} where mb_email = :mb_email ",
        array('mb_email' => $email)
    );
}

function member_count_by_email($email)
{
    $member_table = member_get_member_table_name();

    return (int) sql_fetch_value_prepared(
        " select count(*) as cnt from {$member_table} where mb_email = :mb_email ",
        array('mb_email' => $email)
    );
}

function member_store_password_lost_certify($mb_id, $mb_lost_certify)
{
    $member_table = member_get_member_table_name();

    return sql_query_prepared(
        " update {$member_table} set mb_lost_certify = :mb_lost_certify where mb_id = :mb_id ",
        array(
            'mb_lost_certify' => $mb_lost_certify,
            'mb_id' => $mb_id,
        )
    );
}

function member_find_by_id_and_dupinfo($mb_id, $mb_dupinfo)
{
    $member_table = member_get_member_table_name();

    return sql_fetch_prepared(
        "select * from {$member_table} where mb_id = :mb_id AND mb_dupinfo = :mb_dupinfo",
        array(
            'mb_id' => $mb_id,
            'mb_dupinfo' => $mb_dupinfo,
        )
    );
}

function member_update_password_by_dupinfo($mb_id, $mb_dupinfo, $mb_password)
{
    $member_table = member_get_member_table_name();

    return sql_query_prepared(
        "update {$member_table} set mb_password = :mb_password where mb_id = :mb_id AND mb_dupinfo = :mb_dupinfo",
        array(
            'mb_password' => $mb_password,
            'mb_id' => $mb_id,
            'mb_dupinfo' => $mb_dupinfo,
        )
    );
}

function member_find_login_member($mb_id)
{
    return get_member($mb_id);
}

function member_find_password_lost_certify_row($mb_no)
{
    $member_table = member_get_member_table_name();

    return sql_fetch_prepared(
        " select mb_id, mb_lost_certify from {$member_table} where mb_no = :mb_no ",
        array('mb_no' => $mb_no)
    );
}

function member_confirm_password_lost_certify($mb_no, $lost_certify, $new_password_hash)
{
    $member_table = member_get_member_table_name();

    if (!sql_begin_transaction()) {
        die('Error');
    }

    $updated = sql_query_prepared(
        " update {$member_table} set mb_lost_certify = '', mb_password = :mb_password where mb_no = :mb_no and mb_lost_certify = :mb_lost_certify ",
        array(
            'mb_password' => $new_password_hash,
            'mb_no' => $mb_no,
            'mb_lost_certify' => $lost_certify,
        ),
        false
    );

    if (!$updated || sql_affected_rows($updated) < 1) {
        sql_rollback();
        die('Error');
    }

    if (!sql_commit()) {
        sql_rollback();
        die('Error');
    }
}
