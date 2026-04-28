<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 목록 화면에 넘길 view-model만 조립한다.
// DB 조회는 member-list-query.lib.php, 선택수정/삭제 처리는 member-list-update.lib.php를 확인한다.
// 이 파일에 POST 처리나 저장 로직을 추가하지 않는다.

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

function admin_build_member_list_filter_query(array $request, array $overrides = array())
{
    $query = array(
        'sfl' => $request['sfl'],
        'stx' => $request['stx'],
    );

    if ($request['quick_view'] !== 'all') {
        $query['quick_view'] = $request['quick_view'];
    }

    return http_build_query(array_merge($query, $overrides), '', '&');
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

    $query = array(
        'sfl' => $request['sfl'],
        'stx' => $request['stx'],
        'sst' => $column,
        'sod' => $direction,
        'page' => $request['page'],
    );

    if ($request['quick_view'] !== 'all') {
        $query['quick_view'] = $request['quick_view'];
    }

    return http_build_query($query, '', '&');
}

function admin_build_member_list_sort_url(array $request, $column, $flag = 'asc')
{
    return '?' . str_replace('&', '&amp;', admin_build_member_list_sort_query($request, $column, $flag));
}

function admin_build_member_list_summary_links(array $request, $quick_view, $intercept_count_text, $leave_count_text)
{
    return array(
        array(
            'href_attr' => admin_escape_attr('?' . admin_build_member_list_filter_query($request, array('quick_view' => 'blocked', 'sst' => 'mb_intercept_date', 'sod' => 'desc', 'page' => 1))),
            'label_text' => '차단',
            'count_text' => $intercept_count_text,
            'aria_current_attr' => $quick_view === 'blocked' ? ' aria-current="page"' : '',
        ),
        array(
            'href_attr' => admin_escape_attr('?' . admin_build_member_list_filter_query($request, array('quick_view' => 'left', 'sst' => 'mb_leave_date', 'sod' => 'desc', 'page' => 1))),
            'label_text' => '탈퇴',
            'count_text' => $leave_count_text,
            'aria_current_attr' => $quick_view === 'left' ? ' aria-current="page"' : '',
        ),
    );
}

function admin_build_member_list_actions(array $row, array $member, $is_admin, $qstr)
{
    $actions = array();

    if ($is_admin != 'group' && !member_is_left($row)) {
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
    $server_input = g5_get_runtime_server_input();
    $page_data = admin_fetch_member_list_page_data($request, $member, $is_admin);

    $items = array();
    foreach ($page_data['rows'] as $row) {
        $items[] = admin_build_member_list_item($row, $member, $is_admin, $qstr);
    }

    $quick_view = $request['quick_view'];

    $hidden_fields = array(
        'sst' => $request['sst'],
        'sod' => $request['sod'],
        'sfl' => $request['sfl'],
        'stx' => $request['stx'],
        'quick_view' => $request['quick_view'],
        'page' => $request['page'],
    );
    $paging_url = '?' . $qstr . '&amp;page=';
    $leave_count_text = admin_format_count_text($page_data['leave_count'], '명');
    $intercept_count_text = admin_format_count_text($page_data['intercept_count'], '명');

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
            'quick_view' => $request['quick_view'],
            'quick_view_attr' => admin_escape_attr($request['quick_view']),
        ),
        'table_columns' => admin_build_member_list_table_columns($request),
        'hidden_fields' => admin_build_hidden_field_views($hidden_fields),
        'caption' => '회원관리 목록',
        'total_count' => $page_data['total_count'],
        'total_count_text' => admin_format_count_text($page_data['total_count'], '명'),
        'total_page' => $page_data['total_page'],
        'leave_count' => $page_data['leave_count'],
        'leave_count_text' => $leave_count_text,
        'intercept_count' => $page_data['intercept_count'],
        'intercept_count_text' => $intercept_count_text,
        'items' => $items,
        'colspan' => 8,
        'empty_message' => '자료가 없습니다.',
        'admin_token' => get_admin_token(),
        'paging_url' => $paging_url,
        'paging_html' => get_paging(G5_ADMIN_PAGING_PAGES, $request['page'], $page_data['total_page'], $paging_url),
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
