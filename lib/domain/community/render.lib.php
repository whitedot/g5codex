<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_escape_attr($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function community_sanitize_input($value)
{
    return htmlspecialchars(strip_tags((string) $value), ENT_QUOTES, 'UTF-8');
}

function community_escape_textarea($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function community_category_options(array $categories, $selected_id)
{
    $options = array();
    foreach ($categories as $category) {
        $category_id = (int) $category['category_id'];
        $options[] = array(
            'value_attr' => (string) $category_id,
            'label_text' => get_text($category['name']),
            'selected_attr' => $category_id === (int) $selected_id ? ' selected' : '',
        );
    }

    return $options;
}

function community_build_post_item(array $row, $can_read_secret)
{
    $is_secret = !empty($row['is_secret']);
    $title = $is_secret && !$can_read_secret ? '비밀글입니다.' : $row['title'];

    return array(
        'post_id_text' => (int) $row['post_id'],
        'title_text' => get_text($title),
        'author_text' => get_text($row['mb_id']),
        'date_text' => get_text(substr($row['created_at'], 0, 16)),
        'comment_count_text' => (int) $row['comment_count'],
        'is_notice' => !empty($row['is_notice']),
        'is_secret' => $is_secret,
        'view_url_attr' => community_escape_attr(G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($row['board_id']) . '&post_id=' . (int) $row['post_id']),
    );
}

function community_build_list_view(array $request, array $board, array $member, $is_admin)
{
    $categories = !empty($board['use_category']) ? community_fetch_board_categories($board['board_id']) : array();
    $page_data = community_fetch_post_list_page($board['board_id'], $request);
    $items = array();

    foreach ($page_data['rows'] as $row) {
        $items[] = community_build_post_item($row, community_can_view_secret_post($row, $member, $is_admin));
    }

    $total_page = $request['page_rows'] > 0 ? (int) ceil($page_data['total_count'] / $request['page_rows']) : 1;
    $base_url = G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($board['board_id']);
    if ($request['category_id'] > 0) {
        $base_url .= '&amp;category_id=' . (int) $request['category_id'];
    }
    $base_url .= '&amp;page=';

    return array(
        'title' => get_text($board['name']),
        'board_id_attr' => community_escape_attr($board['board_id']),
        'board_name_text' => get_text($board['name']),
        'description_text' => get_text($board['description']),
        'items' => $items,
        'category_options' => community_category_options($categories, $request['category_id']),
        'category_action_attr' => community_escape_attr(G5_COMMUNITY_URL . '/board.php'),
        'write_url_attr' => community_escape_attr(G5_COMMUNITY_URL . '/write.php?board_id=' . rawurlencode($board['board_id'])),
        'can_write' => community_can_write_board($board, $member),
        'empty_message' => '등록된 게시글이 없습니다.',
        'paging_html' => get_paging(G5_ADMIN_PAGING_PAGES, $request['page'], max(1, $total_page), $base_url),
    );
}

function community_build_view_view(array $board, array $post, array $member, $is_admin)
{
    $can_view_content = community_can_view_secret_post($post, $member, $is_admin);
    $content = $can_view_content ? nl2br(get_text($post['content'])) : '비밀글은 작성자와 관리자만 열람할 수 있습니다.';
    $comments = array();

    if ($can_view_content && !empty($board['use_comment'])) {
        foreach (community_fetch_post_comments($post['post_id']) as $comment) {
            $comments[] = array(
                'comment_id_attr' => (int) $comment['comment_id'],
                'author_text' => get_text($comment['mb_id']),
                'date_text' => get_text(substr($comment['created_at'], 0, 16)),
                'content_html' => nl2br(get_text($comment['content'])),
                'can_edit' => community_can_edit_comment($comment, $member, $is_admin),
            );
        }
    }

    return array(
        'title' => get_text($post['title']),
        'board_name_text' => get_text($board['name']),
        'title_text' => get_text($post['title']),
        'author_text' => get_text($post['mb_id']),
        'date_text' => get_text(substr($post['created_at'], 0, 16)),
        'content_html' => $content,
        'list_url_attr' => community_escape_attr(G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($board['board_id'])),
        'write_url_attr' => community_escape_attr(G5_COMMUNITY_URL . '/write.php?board_id=' . rawurlencode($board['board_id'])),
        'edit_url_attr' => community_escape_attr(G5_COMMUNITY_URL . '/write.php?board_id=' . rawurlencode($board['board_id']) . '&post_id=' . (int) $post['post_id']),
        'delete_action_attr' => community_escape_attr(G5_COMMUNITY_URL . '/delete.php'),
        'board_id_attr' => community_escape_attr($board['board_id']),
        'post_id_attr' => (int) $post['post_id'],
        'token' => get_token(),
        'can_write' => community_can_write_board($board, $member),
        'can_edit' => community_can_edit_post($post, $member, $is_admin),
        'can_comment' => $can_view_content && community_can_comment_board($board, $member),
        'comments' => $comments,
        'comment_action_attr' => community_escape_attr(G5_COMMUNITY_URL . '/comment_update.php'),
        'comment_delete_action_attr' => community_escape_attr(G5_COMMUNITY_URL . '/comment_delete.php'),
    );
}

function community_build_form_view(array $board, array $post, array $member, $is_admin)
{
    $is_update = isset($post['post_id']) && (int) $post['post_id'] > 0;
    $categories = !empty($board['use_category']) ? community_fetch_board_categories($board['board_id']) : array();

    return array(
        'title' => $is_update ? '게시글 수정' : '게시글 작성',
        'board_name_text' => get_text($board['name']),
        'form_action_attr' => community_escape_attr(G5_COMMUNITY_URL . '/write_update.php'),
        'list_url_attr' => community_escape_attr(G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($board['board_id'])),
        'board_id_attr' => community_escape_attr($board['board_id']),
        'post_id_attr' => $is_update ? (int) $post['post_id'] : 0,
        'title_value' => community_sanitize_input($is_update ? $post['title'] : ''),
        'content_value' => community_escape_textarea($is_update ? $post['content'] : ''),
        'is_secret_checked' => $is_update && !empty($post['is_secret']) ? ' checked' : '',
        'is_notice_checked' => $is_update && !empty($post['is_notice']) ? ' checked' : '',
        'notice_order_value' => $is_update ? (int) $post['notice_order'] : 0,
        'category_options' => community_category_options($categories, $is_update ? $post['category_id'] : 0),
        'use_category' => !empty($board['use_category']),
        'is_admin' => $is_admin,
        'token' => get_token(),
    );
}
