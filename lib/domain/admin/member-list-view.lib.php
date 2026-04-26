<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function admin_member_list_search_field_labels()
{
    return array(
        'mb_id' => '회원아이디',
        'mb_nick' => '닉네임',
        'mb_name' => '이름',
        'mb_level' => '권한',
        'mb_email' => 'E-MAIL',
        'mb_hp' => '휴대폰번호',
        'mb_datetime' => '가입일시',
        'mb_ip' => 'IP',
    );
}

function admin_build_member_list_search_field_options(array $request)
{
    $options = array();
    foreach (admin_member_list_search_field_labels() as $value => $label) {
        $options[] = admin_build_select_option_view($value, $label, $request['sfl'] === $value);
    }

    return $options;
}

function admin_build_member_list_table_columns(array $request)
{
    return array(
        array(
            'id_attr' => 'mb_list_id',
            'label_text' => '아이디',
            'href_attr' => admin_build_member_list_sort_url($request, 'mb_id'),
            'class_attr' => '',
        ),
        array(
            'id_attr' => 'mb_list_name',
            'label_text' => '이름',
            'href_attr' => admin_build_member_list_sort_url($request, 'mb_name'),
            'class_attr' => '',
        ),
        array(
            'id_attr' => 'mb_list_nick',
            'label_text' => '닉네임',
            'href_attr' => admin_build_member_list_sort_url($request, 'mb_nick'),
            'class_attr' => '',
        ),
        array(
            'id_attr' => 'mb_list_email',
            'label_text' => '이메일 주소',
            'href_attr' => admin_build_member_list_sort_url($request, 'mb_email'),
            'class_attr' => '',
        ),
        array(
            'id_attr' => 'mb_list_level',
            'label_text' => '권한',
            'href_attr' => admin_build_member_list_sort_url($request, 'mb_level', 'desc'),
            'class_attr' => '',
        ),
        array(
            'id_attr' => 'mb_list_status',
            'label_text' => '상태',
            'href_attr' => '',
            'class_attr' => '',
        ),
        array(
            'id_attr' => 'mb_list_mng',
            'label_text' => '관리',
            'href_attr' => '',
            'class_attr' => 'text-end',
        ),
    );
}

function admin_build_member_list_search(array $request, array $member, $is_admin)
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

    return array('sql_search' => $sql_search, 'search_params' => $search_params);
}

function admin_build_member_list_filter_query(array $request, array $overrides = array())
{
    return http_build_query(array_merge(array('sfl' => $request['sfl'], 'stx' => $request['stx']), $overrides), '', '&');
}

function admin_build_member_list_sort_query(array $request, $column, $flag = 'asc')
{
    if (!in_array($column, admin_member_list_allowed_sort_fields(), true)) {
        return admin_build_member_list_filter_query($request);
    }

    $default_direction = strtolower((string) $flag) === 'desc' ? 'desc' : 'asc';
    $direction = $default_direction;

    if ($request['sst'] === $column && $request['sod'] === $default_direction) {
        $direction = $default_direction === 'asc' ? 'desc' : 'asc';
    }

    return http_build_query(array(
        'sfl' => $request['sfl'],
        'stx' => $request['stx'],
        'sst' => $column,
        'sod' => $direction,
        'page' => $request['page'],
    ), '', '&');
}

function admin_build_member_list_sort_url(array $request, $column, $flag = 'asc')
{
    return '?' . str_replace('&', '&amp;', admin_build_member_list_sort_query($request, $column, $flag));
}

function admin_build_member_list_summary_links(array $request, $quick_view, $intercept_count_text, $leave_count_text)
{
    return array(
        array(
            'href_attr' => admin_escape_attr('?' . admin_build_member_list_filter_query($request, array('sst' => 'mb_intercept_date', 'sod' => 'desc'))),
            'label_text' => '차단',
            'count_text' => $intercept_count_text,
            'aria_current_attr' => $quick_view === 'blocked' ? ' aria-current="page"' : '',
        ),
        array(
            'href_attr' => admin_escape_attr('?' . admin_build_member_list_filter_query($request, array('sst' => 'mb_leave_date', 'sod' => 'desc'))),
            'label_text' => '탈퇴',
            'count_text' => $leave_count_text,
            'aria_current_attr' => $quick_view === 'left' ? ' aria-current="page"' : '',
        ),
    );
}

function admin_build_member_list_actions(array $row, array $member, $is_admin, $qstr)
{
    $actions = array();

    if ($is_admin != 'group') {
        $actions[] = array(
            'type' => 'link',
            'href_attr' => './member_form.php?' . $qstr . '&amp;w=u&amp;mb_id=' . rawurlencode((string) $row['mb_id']),
            'label_text' => '수정',
            'class_attr' => 'btn btn-sm btn-surface-default-soft',
            'mb_id_attr' => '',
        );
    }

    if ($member['mb_id'] != $row['mb_id'] && is_admin($row['mb_id']) != 'super' && ($is_admin == 'super' || $row['mb_level'] < $member['mb_level'])) {
        $actions[] = array(
            'type' => 'delete',
            'href_attr' => '',
            'label_text' => '삭제',
            'class_attr' => 'btn btn-sm btn-outline-danger',
            'mb_id_attr' => admin_escape_attr($row['mb_id']),
        );
    }

    return $actions;
}

function admin_build_member_list_item(array $row, array $member, $is_admin, $qstr)
{
    $status_label = '정상';
    $status_class = 'is-normal';

    if ($row['mb_leave_date']) {
        $status_label = '탈퇴';
        $status_class = 'is-left';
    } elseif ($row['mb_intercept_date']) {
        $status_label = '차단';
        $status_class = 'is-blocked';
    }

    return array(
        'mb_id' => $row['mb_id'],
        'display_mb_id' => member_get_display_id($row),
        'mb_name' => get_text($row['mb_name']),
        'mb_nick_text' => get_text($row['mb_nick']),
        'sideview_html' => get_sideview($row['mb_id'], get_text($row['mb_nick']), get_text($row['mb_email'])),
        'mb_email' => get_text($row['mb_email']),
        'mb_level' => (int) $row['mb_level'],
        'status_label' => $status_label,
        'status_class' => $status_class,
        'actions' => admin_build_member_list_actions($row, $member, $is_admin, $qstr),
    );
}

function admin_build_member_list_view(array $request, array $member, $is_admin, array $config, $qstr)
{
    global $g5;

    $server_input = g5_get_runtime_server_input();
    $sql_common = " from {$g5['member_table']} ";
    $search = admin_build_member_list_search($request, $member, $is_admin);
    $sql_search = $search['sql_search'];
    $search_params = $search['search_params'];
    $sql_order = " order by {$request['sst']} {$request['sod']} ";

    $total_count = (int) sql_fetch_value_prepared(" select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ", $search_params);
    $total_page = (int) ceil($total_count / $request['rows']);
    $from_record = ($request['page'] - 1) * $request['rows'];
    $leave_count = (int) sql_fetch_value_prepared(" select count(*) as cnt {$sql_common} {$sql_search} and mb_leave_date <> '' {$sql_order} ", $search_params);
    $intercept_count = (int) sql_fetch_value_prepared(" select count(*) as cnt {$sql_common} {$sql_search} and mb_intercept_date <> '' {$sql_order} ", $search_params);

    $list_params = $search_params;
    $list_params['from_record'] = (int) $from_record;
    $list_params['page_rows'] = (int) $request['rows'];
    $result = sql_query_prepared(" select * {$sql_common} {$sql_search} {$sql_order} limit :from_record, :page_rows ", $list_params);

    $items = array();
    for ($i = 0; $row = sql_fetch_array($result); $i++) {
        $items[] = admin_build_member_list_item($row, $member, $is_admin, $qstr);
    }

    $quick_view = 'all';
    if ($request['sst'] === 'mb_intercept_date' && $request['sod'] === 'desc') {
        $quick_view = 'blocked';
    } elseif ($request['sst'] === 'mb_leave_date' && $request['sod'] === 'desc') {
        $quick_view = 'left';
    }

    $hidden_fields = array(
        'sst' => $request['sst'],
        'sod' => $request['sod'],
        'sfl' => $request['sfl'],
        'stx' => $request['stx'],
        'page' => $request['page'],
    );
    $paging_url = '?' . $qstr . '&amp;page=';
    $leave_count_text = admin_format_count_text($leave_count, '명');
    $intercept_count_text = admin_format_count_text($intercept_count, '명');

    return array(
        'list_all_url_attr' => admin_escape_attr(isset($server_input['SCRIPT_NAME']) ? $server_input['SCRIPT_NAME'] : ''),
        'summary_filter_links' => admin_build_member_list_summary_links($request, $quick_view, $intercept_count_text, $leave_count_text),
        'sort_urls' => array(
            'mb_id' => admin_build_member_list_sort_url($request, 'mb_id'),
            'mb_name' => admin_build_member_list_sort_url($request, 'mb_name'),
            'mb_nick' => admin_build_member_list_sort_url($request, 'mb_nick'),
            'mb_email' => admin_build_member_list_sort_url($request, 'mb_email'),
            'mb_level' => admin_build_member_list_sort_url($request, 'mb_level', 'desc'),
        ),
        'search_view' => array(
            'field_options' => admin_build_member_list_search_field_options($request),
            'stx_value' => get_sanitize_input($request['stx']),
        ),
        'table_columns' => admin_build_member_list_table_columns($request),
        'hidden_fields' => admin_build_hidden_field_views($hidden_fields),
        'caption' => '회원관리 목록',
        'total_count' => $total_count,
        'total_count_text' => admin_format_count_text($total_count, '명'),
        'total_page' => $total_page,
        'leave_count' => $leave_count,
        'leave_count_text' => $leave_count_text,
        'intercept_count' => $intercept_count,
        'intercept_count_text' => $intercept_count_text,
        'items' => $items,
        'colspan' => 8,
        'empty_message' => '자료가 없습니다.',
        'admin_token' => get_admin_token(),
        'paging_url' => $paging_url,
        'paging_html' => get_paging(G5_ADMIN_PAGING_PAGES, $request['page'], $total_page, $paging_url),
        'title' => '회원관리',
        'admin_container_class' => 'admin-page-member-list',
        'admin_page_subtitle' => '회원 상태를 한눈에 확인하고, 조건 검색과 빠른 관리 동선을 자연스럽게 이어가세요.',
        'show_add_button' => $is_admin == 'super',
        'add_member_url' => './member_form.php',
        'notice_title' => '회원 삭제 안내',
        'notice_body' => '회원자료 삭제 시 로그인은 즉시 차단되며, 운영상 필요한 회원아이디와 상태 정보만 남기고 이름·닉네임·이메일·휴대폰·생년월일·IP·인증이력 등 식별 가능한 개인정보는 비식별 처리 또는 삭제됩니다.',
    );
}

function admin_build_member_list_page_view(array $request, array $member, $is_admin, array $config, $qstr)
{
    return admin_build_member_list_view($request, $member, $is_admin, $config, $qstr);
}
