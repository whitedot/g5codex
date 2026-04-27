<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function member_find_email_certify_candidate($mb_id)
{
    $member_table = member_get_member_table_name();

    return sql_fetch_prepared(
        " select mb_id, mb_email_certify2, mb_leave_date, mb_intercept_date from {$member_table} where mb_id = :mb_id ",
        array('mb_id' => $mb_id)
    );
}

function member_clear_email_certify_token($mb_id)
{
    $member_table = member_get_member_table_name();

    return sql_query_prepared(
        " update {$member_table} set mb_email_certify2 = '' where mb_id = :mb_id ",
        array('mb_id' => $mb_id)
    );
}

function member_mark_email_certified($mb_id)
{
    $member_table = member_get_member_table_name();

    return sql_query_prepared(
        " update {$member_table} set mb_email_certify = :mb_email_certify where mb_id = :mb_id ",
        array(
            'mb_email_certify' => G5_TIME_YMDHIS,
            'mb_id' => $mb_id,
        )
    );
}

function member_find_register_email_member($mb_id)
{
    $member_table = member_get_member_table_name();

    return sql_fetch_prepared(
        " select mb_email, mb_datetime, mb_ip, mb_email_certify, mb_id from {$member_table} where mb_id = :mb_id ",
        array('mb_id' => $mb_id)
    );
}

function member_find_pending_register_email_member($mb_id)
{
    $member_table = member_get_member_table_name();

    return sql_fetch_prepared(
        " select mb_name from {$member_table} where mb_id = :mb_id and substring(mb_email_certify, 1, 1) = '0' ",
        array('mb_id' => $mb_id)
    );
}

function member_count_other_members_by_email($mb_id, $mb_email)
{
    $member_table = member_get_member_table_name();

    return (int) sql_fetch_value_prepared(
        " select count(*) as cnt from {$member_table} where mb_id <> :mb_id and mb_email = :mb_email ",
        array(
            'mb_id' => $mb_id,
            'mb_email' => $mb_email,
        )
    );
}

function member_update_email_address($mb_id, $mb_email)
{
    $member_table = member_get_member_table_name();

    return sql_query_prepared(
        " update {$member_table} set mb_email = :mb_email where mb_id = :mb_id ",
        array(
            'mb_email' => $mb_email,
            'mb_id' => $mb_id,
        )
    );
}
