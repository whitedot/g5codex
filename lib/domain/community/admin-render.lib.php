<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_admin_status_label($status)
{
    $labels = array(
        'active' => '사용',
        'hidden' => '숨김',
        'archived' => '보관',
    );

    return isset($labels[$status]) ? $labels[$status] : $status;
}

function community_admin_status_class($status)
{
    $classes = array(
        'active' => 'is-active',
        'hidden' => 'is-hidden',
        'archived' => 'is-archived',
        'published' => 'is-active',
        'deleted' => 'is-deleted',
        'ok' => 'is-active',
        'warning' => 'is-warning',
        'error' => 'is-deleted',
    );

    return isset($classes[$status]) ? $classes[$status] : 'is-muted';
}

function community_admin_flag_class($enabled)
{
    return !empty($enabled) ? 'is-on' : 'is-off';
}

function community_admin_build_status_options($selected)
{
    $options = array();
    foreach (community_admin_board_status_values() as $status) {
        $options[] = admin_build_select_option_view($status, community_admin_status_label($status), $selected === $status);
    }

    return $options;
}

function community_admin_build_board_item(array $row)
{
    return array(
        'board_id_text' => get_text($row['board_id']),
        'name_text' => get_text($row['name']),
        'status_text' => get_text(community_admin_status_label($row['status'])),
        'status_class' => community_admin_status_class($row['status']),
        'read_level_text' => (int) $row['read_level'],
        'write_level_text' => (int) $row['write_level'],
        'comment_level_text' => (int) $row['comment_level'],
        'use_category_text' => !empty($row['use_category']) ? '사용' : '미사용',
        'use_category_class' => community_admin_flag_class($row['use_category']),
        'use_comment_text' => !empty($row['use_comment']) ? '사용' : '미사용',
        'use_comment_class' => community_admin_flag_class($row['use_comment']),
        'use_latest_text' => !empty($row['use_latest']) ? '사용' : '미사용',
        'use_latest_class' => community_admin_flag_class($row['use_latest']),
        'edit_url_attr' => admin_escape_attr('./community_board_form.php?board_id=' . rawurlencode($row['board_id'])),
    );
}

function community_admin_build_board_list_view(array $request, array $config)
{
    $page_data = community_admin_fetch_board_list_page($request);
    $items = array();

    foreach ($page_data['rows'] as $row) {
        $items[] = community_admin_build_board_item($row);
    }

    $total_page = $request['page_rows'] > 0 ? (int) ceil($page_data['total_count'] / $request['page_rows']) : 1;
    $qstr = community_admin_build_board_list_qstr($request, array('page' => ''));
    $paging_url = './community_board_list.php';
    $paging_url .= $qstr !== '' ? '?' . $qstr . '&amp;page=' : '?page=';

    return array(
        'title' => '커뮤니티 게시판 관리',
        'admin_container_class' => 'admin-page-community-board-list',
        'admin_page_subtitle' => '커뮤니티 게시판과 운영 설정을 관리합니다.',
        'total_count_text' => admin_format_count_text($page_data['total_count'], '개'),
        'items' => $items,
        'empty_message' => '등록된 커뮤니티 게시판이 없습니다.',
        'add_url_attr' => admin_escape_attr('./community_board_form.php'),
        'list_all_url_attr' => admin_escape_attr('./community_board_list.php'),
        'search_action_attr' => admin_escape_attr('./community_board_list.php'),
        'stx_value' => get_sanitize_input($request['stx']),
        'status_options' => array_merge(
            array(admin_build_select_option_view('', '전체', $request['status'] === '')),
            community_admin_build_status_options($request['status'])
        ),
        'paging_html' => get_paging(G5_ADMIN_PAGING_PAGES, $request['page'], max(1, $total_page), $paging_url),
    );
}

function community_admin_default_board_row()
{
    $community_config = community_get_config();

    return array(
        'board_id' => '',
        'name' => '',
        'description' => '',
        'read_level' => $community_config['board_read_level'],
        'write_level' => $community_config['board_write_level'],
        'comment_level' => $community_config['board_comment_level'],
        'list_order' => 0,
        'use_category' => $community_config['board_use_category'],
        'use_latest' => $community_config['board_use_latest'],
        'use_comment' => $community_config['board_use_comment'],
        'use_mail_post' => $community_config['board_use_mail_post'],
        'use_mail_comment' => $community_config['board_use_mail_comment'],
        'mail_admin' => $community_config['board_mail_admin'],
        'upload_file_count' => $community_config['board_upload_file_count'],
        'upload_file_size' => $community_config['board_upload_file_size'],
        'upload_extensions' => $community_config['board_upload_extensions'],
        'use_point' => $community_config['board_use_point'],
        'point_write' => $community_config['board_point_write'],
        'point_comment' => $community_config['board_point_comment'],
        'point_read' => $community_config['board_point_read'],
        'status' => 'active',
    );
}

function community_admin_build_config_form_view()
{
    $community_config = community_get_config();
    $expire_days = (int) $community_config['point_expire_days'];
    $point_expire_rule = $expire_days > 0
        ? '현재 기준: 새로 지급되는 포인트는 지급 시점부터 ' . number_format($expire_days) . '일 뒤 만료됩니다.'
        : '현재 기준: 새로 지급되는 포인트는 만료되지 않습니다.';

    return array(
        'title' => '커뮤니티 기본환경 설정',
        'admin_container_class' => 'admin-page-community-config-form',
        'admin_page_subtitle' => '포인트 만료 기준과 신규 게시판에 공통 적용할 기본값을 설정합니다.',
        'form_action_attr' => admin_escape_attr('./community_config_form_update.php'),
        'board_list_url_attr' => admin_escape_attr('./community_board_list.php'),
        'point_expire_days_value' => $expire_days,
        'point_expire_rule_text' => get_text($point_expire_rule),
        'board_read_level_options' => admin_build_member_level_options(1, 10, $community_config['board_read_level']),
        'board_write_level_options' => admin_build_member_level_options(1, 10, $community_config['board_write_level']),
        'board_comment_level_options' => admin_build_member_level_options(1, 10, $community_config['board_comment_level']),
        'board_use_category_checked' => !empty($community_config['board_use_category']) ? ' checked' : '',
        'board_use_latest_checked' => !empty($community_config['board_use_latest']) ? ' checked' : '',
        'board_use_comment_checked' => !empty($community_config['board_use_comment']) ? ' checked' : '',
        'board_use_mail_post_checked' => !empty($community_config['board_use_mail_post']) ? ' checked' : '',
        'board_use_mail_comment_checked' => !empty($community_config['board_use_mail_comment']) ? ' checked' : '',
        'board_mail_admin_checked' => !empty($community_config['board_mail_admin']) ? ' checked' : '',
        'board_upload_file_count_value' => (int) $community_config['board_upload_file_count'],
        'board_upload_file_size_value' => (int) $community_config['board_upload_file_size'],
        'board_upload_extensions_value' => get_sanitize_input($community_config['board_upload_extensions']),
        'board_use_point_checked' => !empty($community_config['board_use_point']) ? ' checked' : '',
        'board_point_write_value' => (int) $community_config['board_point_write'],
        'board_point_comment_value' => (int) $community_config['board_point_comment'],
        'board_point_read_value' => (int) $community_config['board_point_read'],
        'admin_token' => get_admin_token(),
    );
}

function community_admin_category_text(array $categories)
{
    $names = array();
    foreach ($categories as $category) {
        $names[] = $category['name'];
    }

    return implode("\n", $names);
}

function community_admin_build_board_form_view(array $request)
{
    $is_update = ($request['board_id'] !== '');
    $board = $is_update ? community_admin_fetch_board($request['board_id']) : array();

    if ($is_update && !(isset($board['board_id']) && $board['board_id'] !== '')) {
        alert('존재하지 않는 커뮤니티 게시판입니다.', './community_board_list.php');
    }

    $board = array_merge(community_admin_default_board_row(), $board);
    $categories = $is_update ? community_admin_fetch_board_categories($request['board_id']) : array();

    return array(
        'title' => $is_update ? '커뮤니티 게시판 수정' : '커뮤니티 게시판 추가',
        'admin_container_class' => 'admin-page-community-board-form',
        'admin_page_subtitle' => '게시판 권한, 카테고리, 알림, 첨부 제한을 설정합니다.',
        'is_update' => $is_update,
        'form_action_attr' => admin_escape_attr('./community_board_form_update.php'),
        'list_url_attr' => admin_escape_attr('./community_board_list.php'),
        'original_board_id_attr' => admin_escape_attr($board['board_id']),
        'board_id_value' => get_sanitize_input($board['board_id']),
        'board_id_readonly_attr' => $is_update ? ' readonly' : '',
        'name_value' => get_sanitize_input($board['name']),
        'description_value' => get_sanitize_input($board['description']),
        'read_level_options' => admin_build_member_level_options(1, 10, $board['read_level']),
        'write_level_options' => admin_build_member_level_options(1, 10, $board['write_level']),
        'comment_level_options' => admin_build_member_level_options(1, 10, $board['comment_level']),
        'list_order_value' => (int) $board['list_order'],
        'use_category_checked' => !empty($board['use_category']) ? ' checked' : '',
        'use_latest_checked' => !empty($board['use_latest']) ? ' checked' : '',
        'use_comment_checked' => !empty($board['use_comment']) ? ' checked' : '',
        'use_mail_post_checked' => !empty($board['use_mail_post']) ? ' checked' : '',
        'use_mail_comment_checked' => !empty($board['use_mail_comment']) ? ' checked' : '',
        'mail_admin_checked' => !empty($board['mail_admin']) ? ' checked' : '',
        'upload_file_count_value' => (int) $board['upload_file_count'],
        'upload_file_size_value' => (int) $board['upload_file_size'],
        'upload_extensions_value' => get_sanitize_input($board['upload_extensions']),
        'use_point_checked' => !empty($board['use_point']) ? ' checked' : '',
        'point_write_value' => (int) $board['point_write'],
        'point_comment_value' => (int) $board['point_comment'],
        'point_read_value' => (int) $board['point_read'],
        'status_options' => community_admin_build_status_options($board['status']),
        'categories_value' => get_sanitize_input(community_admin_category_text($categories)),
        'admin_token' => get_admin_token(),
    );
}

function community_admin_post_status_label($status)
{
    $labels = array(
        'published' => '공개',
        'hidden' => '숨김',
        'deleted' => '삭제',
    );

    return isset($labels[$status]) ? $labels[$status] : $status;
}

function community_admin_build_post_status_options($selected)
{
    $options = array(admin_build_select_option_view('', '전체', $selected === ''));
    foreach (community_admin_post_status_values() as $status) {
        $options[] = admin_build_select_option_view($status, community_admin_post_status_label($status), $selected === $status);
    }

    return $options;
}

function community_admin_build_post_item(array $row)
{
    return array(
        'post_id_attr' => (int) $row['post_id'],
        'post_id_text' => (int) $row['post_id'],
        'board_id_text' => get_text($row['board_id']),
        'title_text' => get_text($row['title']),
        'author_text' => get_text($row['mb_id']),
        'status_text' => get_text(community_admin_post_status_label($row['status'])),
        'status_class' => community_admin_status_class($row['status']),
        'notice_text' => !empty($row['is_notice']) ? '공지' : '',
        'comment_count_text' => (int) $row['comment_count'],
        'attachment_count_text' => (int) $row['attachment_count'],
        'created_at_text' => get_text($row['created_at']),
        'view_url_attr' => admin_escape_attr(G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($row['board_id']) . '&post_id=' . (int) $row['post_id']),
        'comment_url_attr' => admin_escape_attr('./community_comment_list.php?post_id=' . (int) $row['post_id']),
    );
}

function community_admin_build_post_list_view(array $request, array $config)
{
    $page_data = community_admin_fetch_post_list_page($request);
    $items = array();

    foreach ($page_data['rows'] as $row) {
        $items[] = community_admin_build_post_item($row);
    }

    $total_page = $request['page_rows'] > 0 ? (int) ceil($page_data['total_count'] / $request['page_rows']) : 1;
    $qstr = community_admin_build_post_list_qstr($request, array('page' => ''));
    $paging_url = './community_post_list.php';
    $paging_url .= $qstr !== '' ? '?' . $qstr . '&amp;page=' : '?page=';

    return array(
        'title' => '커뮤니티 게시글 관리',
        'admin_container_class' => 'admin-page-community-post-list',
        'admin_page_subtitle' => '커뮤니티 게시글을 검색하고 공개, 숨김, 삭제, 공지 상태를 관리합니다.',
        'total_count_text' => admin_format_count_text($page_data['total_count'], '건'),
        'items' => $items,
        'empty_message' => '게시글이 없습니다.',
        'search_action_attr' => admin_escape_attr('./community_post_list.php'),
        'update_action_attr' => admin_escape_attr('./community_post_list_update.php'),
        'return_query_attr' => admin_escape_attr(community_admin_build_post_list_qstr($request)),
        'board_id_value' => get_sanitize_input($request['board_id']),
        'stx_value' => get_sanitize_input($request['stx']),
        'status_options' => community_admin_build_post_status_options($request['status']),
        'admin_token' => get_admin_token(),
        'paging_html' => get_paging(G5_ADMIN_PAGING_PAGES, $request['page'], max(1, $total_page), $paging_url),
    );
}

function community_admin_build_comment_item(array $row)
{
    $board_id = isset($row['post_board_id']) ? (string) $row['post_board_id'] : '';
    $post_url = $board_id !== ''
        ? G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($board_id) . '&post_id=' . (int) $row['post_id']
        : './community_post_list.php?post_id=' . (int) $row['post_id'];

    return array(
        'comment_id_attr' => (int) $row['comment_id'],
        'comment_id_text' => (int) $row['comment_id'],
        'post_id_text' => (int) $row['post_id'],
        'post_title_text' => get_text(isset($row['post_title']) ? cut_str($row['post_title'], 60) : ''),
        'author_text' => get_text($row['mb_id']),
        'content_text' => get_text(cut_str($row['content'], 120)),
        'status_text' => get_text(community_admin_post_status_label($row['status'])),
        'status_class' => community_admin_status_class($row['status']),
        'created_at_text' => get_text($row['created_at']),
        'post_url_attr' => admin_escape_attr($post_url),
    );
}

function community_admin_build_comment_list_view(array $request, array $config)
{
    $page_data = community_admin_fetch_comment_list_page($request);
    $items = array();

    foreach ($page_data['rows'] as $row) {
        $items[] = community_admin_build_comment_item($row);
    }

    $total_page = $request['page_rows'] > 0 ? (int) ceil($page_data['total_count'] / $request['page_rows']) : 1;
    $qstr = community_admin_build_comment_list_qstr($request, array('page' => ''));
    $paging_url = './community_comment_list.php';
    $paging_url .= $qstr !== '' ? '?' . $qstr . '&amp;page=' : '?page=';

    return array(
        'title' => '커뮤니티 댓글 관리',
        'admin_container_class' => 'admin-page-community-comment-list',
        'admin_page_subtitle' => '커뮤니티 댓글을 검색하고 공개/삭제 상태를 관리합니다.',
        'total_count_text' => admin_format_count_text($page_data['total_count'], '건'),
        'items' => $items,
        'empty_message' => '댓글이 없습니다.',
        'search_action_attr' => admin_escape_attr('./community_comment_list.php'),
        'update_action_attr' => admin_escape_attr('./community_comment_list_update.php'),
        'return_query_attr' => admin_escape_attr(community_admin_build_comment_list_qstr($request)),
        'post_id_value' => $request['post_id'] > 0 ? (int) $request['post_id'] : '',
        'stx_value' => get_sanitize_input($request['stx']),
        'status_options' => community_admin_build_post_status_options($request['status']),
        'admin_token' => get_admin_token(),
        'paging_html' => get_paging(G5_ADMIN_PAGING_PAGES, $request['page'], max(1, $total_page), $paging_url),
    );
}

function community_admin_build_point_wallet_item(array $row)
{
    return array(
        'mb_id_text' => get_text($row['mb_id']),
        'balance_text' => number_format((int) $row['balance']),
        'earned_text' => number_format((int) $row['earned_total']),
        'spent_text' => number_format((int) $row['spent_total']),
        'expired_text' => number_format((int) $row['expired_total']),
        'updated_at_text' => get_text($row['updated_at']),
    );
}

function community_admin_build_point_ledger_item(array $row)
{
    $amount = (int) $row['amount'];

    return array(
        'ledger_id_text' => (int) $row['ledger_id'],
        'mb_id_text' => get_text($row['mb_id']),
        'amount_text' => number_format($amount),
        'amount_class' => $amount >= 0 ? 'is-positive' : 'is-negative',
        'balance_after_text' => number_format((int) $row['balance_after']),
        'reason_text' => get_text($row['reason']),
        'target_text' => get_text($row['target_type'] . ' #' . (int) $row['target_id']),
        'created_by_text' => get_text($row['created_by']),
        'created_at_text' => get_text($row['created_at']),
    );
}

function community_admin_build_point_list_view(array $request, array $config)
{
    $page_data = community_admin_fetch_point_wallet_page($request);
    $wallet_items = array();
    foreach ($page_data['rows'] as $row) {
        $wallet_items[] = community_admin_build_point_wallet_item($row);
    }

    $ledger_items = array();
    foreach (community_admin_fetch_point_ledger_rows($request['mb_id'], 20) as $row) {
        $ledger_items[] = community_admin_build_point_ledger_item($row);
    }

    $total_page = $request['page_rows'] > 0 ? (int) ceil($page_data['total_count'] / $request['page_rows']) : 1;
    $qstr = community_admin_build_point_list_qstr($request, array('page' => ''));
    $paging_url = './community_point_list.php';
    $paging_url .= $qstr !== '' ? '?' . $qstr . '&amp;page=' : '?page=';

    return array(
        'title' => '커뮤니티 포인트 관리',
        'admin_container_class' => 'admin-page-community-point-list',
        'admin_page_subtitle' => '커뮤니티 전용 포인트 지갑과 원장을 조회하고 수동 조정합니다.',
        'total_count_text' => admin_format_count_text($page_data['total_count'], '명'),
        'wallet_items' => $wallet_items,
        'ledger_items' => $ledger_items,
        'empty_wallet_message' => '포인트 지갑이 없습니다.',
        'empty_ledger_message' => '포인트 원장이 없습니다.',
        'search_action_attr' => admin_escape_attr('./community_point_list.php'),
        'adjust_action_attr' => admin_escape_attr('./community_point_adjust.php'),
        'expire_action_attr' => admin_escape_attr('./community_point_expire.php'),
        'return_query_attr' => admin_escape_attr(community_admin_build_point_list_qstr($request)),
        'mb_id_value' => get_sanitize_input($request['mb_id']),
        'admin_token' => get_admin_token(),
        'paging_html' => get_paging(G5_ADMIN_PAGING_PAGES, $request['page'], max(1, $total_page), $paging_url),
    );
}

function community_admin_health_status_label($status)
{
    $labels = array(
        'ok' => '정상',
        'warning' => '주의',
        'error' => '오류',
    );

    return isset($labels[$status]) ? $labels[$status] : $status;
}

function community_admin_build_health_item(array $row)
{
    return array(
        'label_text' => get_text($row['label']),
        'status_text' => get_text(community_admin_health_status_label($row['status'])),
        'status_class' => community_admin_status_class($row['status']),
        'message_text' => get_text($row['message']),
        'action_text' => get_text($row['action']),
    );
}

function community_admin_build_health_view()
{
    $items = array();
    $counts = array('ok' => 0, 'warning' => 0, 'error' => 0);

    foreach (community_admin_fetch_health_checks() as $row) {
        $status = isset($row['status']) ? $row['status'] : 'warning';
        if (!isset($counts[$status])) {
            $status = 'warning';
        }
        $counts[$status]++;
        $row['status'] = $status;
        $items[] = community_admin_build_health_item($row);
    }

    return array(
        'title' => '커뮤니티 점검',
        'admin_container_class' => 'admin-page-community-health',
        'admin_page_subtitle' => '커뮤니티 운영에 필요한 테이블, 경로, 메일 설정을 확인합니다.',
        'items' => $items,
        'ok_count_text' => admin_format_count_text($counts['ok'], '건'),
        'warning_count_text' => admin_format_count_text($counts['warning'], '건'),
        'error_count_text' => admin_format_count_text($counts['error'], '건'),
    );
}
