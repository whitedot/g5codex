<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 목록 선택수정의 DB update payload와 저장만 담당한다.
// 권한/대상 회원 검증은 member-list-validation.lib.php에서 끝난 상태를 전제로 한다.
// 이 파일에 alert/redirect/event 호출을 추가하지 않는다.

function admin_build_member_list_update_payload(array $request, $index, array $mb)
{
    $post_mb_certify = (!empty($request['mb_certify'][$index])) ? clean_xss_tags($request['mb_certify'][$index], 1, 1, 20) : '';
    $post_mb_level = isset($request['mb_level'][$index]) ? (int) $request['mb_level'][$index] : 0;
    $post_mb_intercept_date = (!empty($request['mb_intercept_date'][$index])) ? clean_xss_tags($request['mb_intercept_date'][$index], 1, 1, 8) : '';
    $post_mb_mailling = isset($request['mb_mailling'][$index]) ? (int) $request['mb_mailling'][$index] : 0;
    $post_mb_mailling_default = isset($request['mb_mailling_default'][$index]) ? (int) $request['mb_mailling_default'][$index] : 0;
    $post_mb_open = isset($request['mb_open'][$index]) ? (int) $request['mb_open'][$index] : 0;
    $mb_adult = $post_mb_certify ? (isset($request['mb_adult'][$index]) ? (int) $request['mb_adult'][$index] : 0) : 0;

    $agree_items = array();
    if ($post_mb_mailling_default != $post_mb_mailling) {
        $agree_items[] = '광고성 이메일 수신(' . ($post_mb_mailling == 1 ? '동의' : '철회') . ')';
    }

    $params = array(
        'mb_level' => $post_mb_level,
        'mb_intercept_date' => $post_mb_intercept_date,
        'mb_mailling' => $post_mb_mailling,
        'mb_open' => $post_mb_open,
        'mb_certify' => $post_mb_certify,
        'mb_adult' => $mb_adult,
        'mb_id' => $mb['mb_id'],
    );

    $sql_mailling_date = '';
    if ($post_mb_mailling_default != $post_mb_mailling) {
        $sql_mailling_date = ' , mb_mailling_date = :mb_mailling_date ';
        $params['mb_mailling_date'] = G5_TIME_YMDHIS;
    }

    $sql_agree_log = '';
    if (!empty($agree_items)) {
        $sql_agree_log = ' , mb_agree_log = :mb_agree_log';
        $params['mb_agree_log'] = '[' . G5_TIME_YMDHIS . ', 회원관리 선택수정] ' . implode(' | ', $agree_items) . "\n" . (isset($mb['mb_agree_log']) ? $mb['mb_agree_log'] : '');
    }

    return array('sql_mailling_date' => $sql_mailling_date, 'sql_agree_log' => $sql_agree_log, 'params' => $params);
}

function admin_apply_member_list_update(array $payload)
{
    global $g5;

    $sql = " update {$g5['member_table']}
                set mb_level = :mb_level,
                    mb_intercept_date = :mb_intercept_date,
                    mb_mailling = :mb_mailling,
                    mb_open = :mb_open,
                    mb_certify = :mb_certify,
                    mb_adult = :mb_adult
                    {$payload['sql_mailling_date']}
                    {$payload['sql_agree_log']}
              where mb_id = :mb_id ";

    sql_query_prepared($sql, $payload['params']);
}
