<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 도메인 runtime helper를 담당한다.
// request 값 정규화는 request.lib.php, 업무 완료 흐름은 flow-*.lib.php에서 처리한다.

function member_get_runtime_tables()
{
    return function_exists('g5_get_runtime_tables') ? g5_get_runtime_tables() : array();
}

function member_get_runtime_config()
{
    return function_exists('g5_get_runtime_config') ? g5_get_runtime_config() : array();
}

function member_get_member_table_name()
{
    return function_exists('g5_get_runtime_table_name')
        ? g5_get_runtime_table_name('member_table')
        : '';
}
