<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function site_admin_escape_textarea($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function site_admin_status_label($status)
{
    $labels = array(
        'active' => '사용',
        'hidden' => '숨김',
    );

    return isset($labels[$status]) ? $labels[$status] : $status;
}

function site_admin_status_class($status)
{
    return $status === 'active' ? 'is-active' : 'is-hidden';
}

function site_admin_format_label($format)
{
    return $format === 'text' ? '텍스트' : 'HTML';
}

function site_admin_build_binary_status_options($selected)
{
    return array(
        admin_build_select_option_view('active', '사용', $selected === 'active'),
        admin_build_select_option_view('hidden', '숨김', $selected === 'hidden'),
    );
}

function site_admin_build_page_format_options($selected)
{
    $options = array();
    foreach (site_admin_page_format_values() as $format) {
        $options[] = admin_build_select_option_view($format, site_admin_format_label($format), $selected === $format);
    }

    return $options;
}

function site_admin_build_page_item(array $row)
{
    return array(
        'page_id_text' => (int) $row['page_id'],
        'title_text' => get_text($row['title']),
        'slug_text' => get_text($row['slug']),
        'summary_text' => get_text($row['summary']),
        'url_attr' => admin_escape_attr(site_page_url($row['slug'])),
        'format_text' => get_text(site_admin_format_label($row['content_format'])),
        'status_text' => get_text(site_admin_status_label($row['status'])),
        'status_class' => site_admin_status_class($row['status']),
        'device_text' => get_text(($row['show_pc'] ? 'PC' : '') . ($row['show_pc'] && $row['show_mobile'] ? ' / ' : '') . ($row['show_mobile'] ? '모바일' : '')),
        'updated_at_text' => get_text($row['updated_at']),
        'edit_url_attr' => admin_escape_attr('./site_page_form.php?page_id=' . (int) $row['page_id']),
    );
}

function site_admin_build_page_list_view(array $request, array $config)
{
    $page_data = site_admin_fetch_page_list_page($request);
    $items = array();
    foreach ($page_data['rows'] as $row) {
        $items[] = site_admin_build_page_item($row);
    }

    $total_page = $request['page_rows'] > 0 ? (int) ceil($page_data['total_count'] / $request['page_rows']) : 1;
    $qstr = site_admin_build_page_list_qstr($request, array('page' => ''));
    $paging_url = './site_page_list.php';
    $paging_url .= $qstr !== '' ? '?' . $qstr . '&amp;page=' : '?page=';

    return array(
        'title' => '페이지 관리',
        'admin_container_class' => 'admin-page-site-page-list',
        'admin_page_subtitle' => '커뮤니티와 분리된 독립 페이지의 주소, 내용, 접근 권한과 노출 상태를 관리합니다.',
        'total_count_text' => admin_format_count_text($page_data['total_count'], '개'),
        'items' => $items,
        'empty_message' => '등록된 페이지가 없습니다.',
        'add_url_attr' => admin_escape_attr('./site_page_form.php'),
        'list_all_url_attr' => admin_escape_attr('./site_page_list.php'),
        'search_action_attr' => admin_escape_attr('./site_page_list.php'),
        'stx_value' => get_sanitize_input($request['stx']),
        'content_format_options' => array_merge(
            array(admin_build_select_option_view('', '전체', $request['content_format'] === '')),
            site_admin_build_page_format_options($request['content_format'])
        ),
        'status_options' => array(
            admin_build_select_option_view('', '전체', $request['status'] === ''),
            admin_build_select_option_view('active', '사용', $request['status'] === 'active'),
            admin_build_select_option_view('hidden', '숨김', $request['status'] === 'hidden'),
        ),
        'paging_html' => get_paging(G5_ADMIN_PAGING_PAGES, $request['page'], max(1, $total_page), $paging_url),
    );
}

function site_admin_default_page_row()
{
    return array(
        'page_id' => 0,
        'slug' => '',
        'title' => '',
        'summary' => '',
        'content' => '',
        'content_format' => 'html',
        'access_level' => 1,
        'show_pc' => 1,
        'show_mobile' => 1,
        'list_order' => 0,
        'status' => 'active',
    );
}

function site_admin_build_page_form_view(array $request)
{
    $is_update = ($request['page_id'] > 0);
    $page = $is_update ? site_admin_fetch_page($request['page_id']) : array();

    if ($is_update && empty($page['page_id'])) {
        alert('존재하지 않는 페이지입니다.', './site_page_list.php');
    }

    $page = array_merge(site_admin_default_page_row(), $page);

    return array(
        'title' => $is_update ? '페이지 수정' : '페이지 추가',
        'admin_container_class' => 'admin-page-site-page-form',
        'admin_page_subtitle' => '독립 페이지의 공개 주소, 본문, 접근 권한과 노출 기기를 설정합니다.',
        'form_action_attr' => admin_escape_attr('./site_page_form_update.php'),
        'list_url_attr' => admin_escape_attr('./site_page_list.php'),
        'page_id_value' => (int) $page['page_id'],
        'slug_value' => get_sanitize_input($page['slug']),
        'title_value' => get_sanitize_input($page['title']),
        'summary_value' => get_sanitize_input($page['summary']),
        'content_value' => site_admin_escape_textarea($page['content']),
        'content_format_options' => site_admin_build_page_format_options($page['content_format']),
        'access_level_options' => admin_build_member_level_options(1, 10, $page['access_level']),
        'show_pc_checked' => !empty($page['show_pc']) ? ' checked' : '',
        'show_mobile_checked' => !empty($page['show_mobile']) ? ' checked' : '',
        'list_order_value' => (int) $page['list_order'],
        'status_options' => site_admin_build_binary_status_options($page['status']),
        'admin_token' => get_admin_token(),
    );
}
