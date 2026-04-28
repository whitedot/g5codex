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
        'read_level_text' => (int) $row['read_level'],
        'write_level_text' => (int) $row['write_level'],
        'comment_level_text' => (int) $row['comment_level'],
        'use_category_text' => !empty($row['use_category']) ? '사용' : '미사용',
        'use_comment_text' => !empty($row['use_comment']) ? '사용' : '미사용',
        'use_latest_text' => !empty($row['use_latest']) ? '사용' : '미사용',
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
    return array(
        'board_id' => '',
        'name' => '',
        'description' => '',
        'read_level' => 1,
        'write_level' => 2,
        'comment_level' => 2,
        'list_order' => 0,
        'use_category' => 0,
        'use_latest' => 1,
        'use_comment' => 1,
        'use_mail_post' => 1,
        'use_mail_comment' => 1,
        'mail_admin' => 0,
        'upload_file_count' => 0,
        'upload_file_size' => 0,
        'upload_extensions' => '',
        'use_point' => 0,
        'point_write' => 0,
        'point_comment' => 0,
        'point_read' => 0,
        'status' => 'active',
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

function community_admin_build_notification_status_options($selected)
{
    $statuses = array('', 'pending', 'sent', 'failed', 'skipped');
    $labels = array(
        '' => '전체',
        'pending' => '대기',
        'sent' => '발송',
        'failed' => '실패',
        'skipped' => '건너뜀',
    );
    $options = array();

    foreach ($statuses as $status) {
        $options[] = admin_build_select_option_view($status, $labels[$status], $selected === $status);
    }

    return $options;
}

function community_admin_build_notification_item(array $row)
{
    return array(
        'notification_id_attr' => (int) $row['notification_id'],
        'id_text' => (int) $row['notification_id'],
        'event_type_text' => get_text($row['event_type']),
        'target_text' => '글 ' . (int) $row['post_id'] . ' / 댓글 ' . (int) $row['comment_id'],
        'recipient_text' => get_text($row['recipient_mb_id'] . ' <' . $row['recipient_email'] . '>'),
        'subject_text' => get_text($row['subject']),
        'status_text' => get_text($row['status']),
        'error_text' => get_text($row['error_message']),
        'sent_at_text' => get_text($row['sent_at']),
        'created_at_text' => get_text($row['created_at']),
    );
}

function community_admin_build_notification_log_view(array $request, array $config)
{
    $page_data = community_admin_fetch_notification_log_page($request);
    $items = array();

    foreach ($page_data['rows'] as $row) {
        $items[] = community_admin_build_notification_item($row);
    }

    $total_page = $request['page_rows'] > 0 ? (int) ceil($page_data['total_count'] / $request['page_rows']) : 1;
    $qstr = community_admin_build_notification_log_qstr($request, array('page' => ''));
    $paging_url = './community_notification_log.php';
    $paging_url .= $qstr !== '' ? '?' . $qstr . '&amp;page=' : '?page=';

    return array(
        'title' => '커뮤니티 알림 로그',
        'admin_container_class' => 'admin-page-community-notification-log',
        'admin_page_subtitle' => '게시글과 댓글 메일 알림 발송 결과를 확인합니다.',
        'total_count_text' => admin_format_count_text($page_data['total_count'], '건'),
        'items' => $items,
        'empty_message' => '알림 로그가 없습니다.',
        'search_action_attr' => admin_escape_attr('./community_notification_log.php'),
        'update_action_attr' => admin_escape_attr('./community_notification_log_update.php'),
        'return_query_attr' => admin_escape_attr(community_admin_build_notification_log_qstr($request)),
        'stx_value' => get_sanitize_input($request['stx']),
        'status_options' => community_admin_build_notification_status_options($request['status']),
        'admin_token' => get_admin_token(),
        'paging_html' => get_paging(G5_ADMIN_PAGING_PAGES, $request['page'], max(1, $total_page), $paging_url),
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
    return array(
        'comment_id_attr' => (int) $row['comment_id'],
        'comment_id_text' => (int) $row['comment_id'],
        'post_id_text' => (int) $row['post_id'],
        'author_text' => get_text($row['mb_id']),
        'content_text' => get_text(cut_str($row['content'], 120)),
        'status_text' => get_text(community_admin_post_status_label($row['status'])),
        'created_at_text' => get_text($row['created_at']),
        'post_url_attr' => admin_escape_attr('./community_post_list.php?stx=' . rawurlencode((string) $row['post_id'])),
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
    return array(
        'ledger_id_text' => (int) $row['ledger_id'],
        'mb_id_text' => get_text($row['mb_id']),
        'amount_text' => number_format((int) $row['amount']),
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
