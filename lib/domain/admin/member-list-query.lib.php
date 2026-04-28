<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 목록의 검색 조건, count, row 조회만 담당한다.
// 화면용 label/URL 조립은 member-list-view.lib.php, 선택수정 저장은 member-list-persist.lib.php를 확인한다.
// 이 파일에 alert/redirect/HTML 출력 로직을 추가하지 않는다.

function admin_build_member_list_search(array $request, array $member, $is_admin, $apply_quick_view = true)
{
    $search_params = array();
    $sql_search = ' where (1) ';

    if ($request['stx'] !== '') {
        if ($request['sfl'] === 'mb_level') {
            $sql_search .= " and {$request['sfl']} = :stx_exact ";
            $search_params['stx_exact'] = (int) $request['stx'];
        } elseif ($request['sfl'] === 'mb_hp') {
            $sql_search .= " and {$request['sfl']} like :stx_suffix ";
            $search_params['stx_suffix'] = '%' . $request['stx'];
        } else {
            $sql_search .= " and {$request['sfl']} like :stx_prefix ";
            $search_params['stx_prefix'] = $request['stx'] . '%';
        }
    }

    if ($is_admin != 'super') {
        $sql_search .= ' and mb_level <= :max_member_level ';
        $search_params['max_member_level'] = (int) $member['mb_level'];
    }

    if ($apply_quick_view) {
        if ($request['quick_view'] === 'blocked') {
            $sql_search .= " and mb_intercept_date <> '' ";
        } elseif ($request['quick_view'] === 'left') {
            $sql_search .= " and mb_leave_date <> '' ";
        }
    }

    return array('sql_search' => $sql_search, 'search_params' => $search_params);
}

function admin_fetch_member_list_page_data(array $request, array $member, $is_admin)
{
    global $g5;

    $sql_common = " from {$g5['member_table']} ";
    $search = admin_build_member_list_search($request, $member, $is_admin);
    $summary_search = admin_build_member_list_search($request, $member, $is_admin, false);
    $sql_search = $search['sql_search'];
    $search_params = $search['search_params'];
    $summary_sql_search = $summary_search['sql_search'];
    $summary_search_params = $summary_search['search_params'];
    $sql_order = " order by {$request['sst']} {$request['sod']} ";

    $total_count = (int) sql_fetch_value_prepared(" select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ", $search_params);
    $total_page = (int) ceil($total_count / $request['rows']);
    $from_record = ($request['page'] - 1) * $request['rows'];
    $leave_count = (int) sql_fetch_value_prepared(" select count(*) as cnt {$sql_common} {$summary_sql_search} and mb_leave_date <> '' {$sql_order} ", $summary_search_params);
    $intercept_count = (int) sql_fetch_value_prepared(" select count(*) as cnt {$sql_common} {$summary_sql_search} and mb_intercept_date <> '' {$sql_order} ", $summary_search_params);

    $list_params = $search_params;
    $list_params['from_record'] = (int) $from_record;
    $list_params['page_rows'] = (int) $request['rows'];
    $result = sql_query_prepared(" select * {$sql_common} {$sql_search} {$sql_order} limit :from_record, :page_rows ", $list_params);

    $rows = array();
    for ($i = 0; $row = sql_fetch_array($result); $i++) {
        $rows[] = $row;
    }

    return array(
        'total_count' => $total_count,
        'total_page' => $total_page,
        'leave_count' => $leave_count,
        'intercept_count' => $intercept_count,
        'rows' => $rows,
    );
}
