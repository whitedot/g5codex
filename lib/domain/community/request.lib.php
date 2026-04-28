<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_read_scalar(array $source, $key, $default = '')
{
    if (!array_key_exists($key, $source) || is_array($source[$key])) {
        return $default;
    }

    return trim((string) $source[$key]);
}

function community_read_board_id(array $source)
{
    return preg_replace('/[^a-z0-9_]/i', '', community_read_scalar($source, 'board_id', ''));
}

function community_read_post_id(array $source)
{
    return max(0, (int) community_read_scalar($source, 'post_id', 0));
}

function community_read_list_request(array $get, array $config)
{
    $page_rows = isset($config['cf_page_rows']) ? (int) $config['cf_page_rows'] : 15;

    return array(
        'board_id' => community_read_board_id($get),
        'page' => max(1, (int) community_read_scalar($get, 'page', 1)),
        'page_rows' => $page_rows > 0 ? $page_rows : 15,
        'category_id' => max(0, (int) community_read_scalar($get, 'category_id', 0)),
        'status' => 'published',
    );
}

function community_read_view_request(array $get)
{
    return array(
        'board_id' => community_read_board_id($get),
        'post_id' => community_read_post_id($get),
    );
}

function community_read_form_request(array $get)
{
    return array(
        'board_id' => community_read_board_id($get),
        'post_id' => community_read_post_id($get),
    );
}

function community_read_save_request(array $post)
{
    return array(
        'board_id' => community_read_board_id($post),
        'post_id' => community_read_post_id($post),
        'category_id' => max(0, (int) community_read_scalar($post, 'category_id', 0)),
        'title' => strip_tags(community_read_scalar($post, 'title', '')),
        'content' => trim((string) community_read_scalar($post, 'content', '')),
        'is_secret' => isset($post['is_secret']) ? 1 : 0,
        'is_notice' => isset($post['is_notice']) ? 1 : 0,
        'notice_order' => (int) community_read_scalar($post, 'notice_order', 0),
    );
}

function community_read_delete_request(array $post)
{
    return array(
        'board_id' => community_read_board_id($post),
        'post_id' => community_read_post_id($post),
    );
}

function community_read_comment_save_request(array $post)
{
    return array(
        'board_id' => community_read_board_id($post),
        'post_id' => community_read_post_id($post),
        'content' => trim((string) community_read_scalar($post, 'content', '')),
    );
}

function community_read_comment_delete_request(array $post)
{
    return array(
        'board_id' => community_read_board_id($post),
        'post_id' => community_read_post_id($post),
        'comment_id' => max(0, (int) community_read_scalar($post, 'comment_id', 0)),
    );
}
