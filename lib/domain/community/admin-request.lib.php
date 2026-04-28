<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_admin_board_status_values()
{
    return array('active', 'hidden', 'archived');
}

function community_admin_read_scalar(array $source, $key, $default = '')
{
    if (!array_key_exists($key, $source) || is_array($source[$key])) {
        return $default;
    }

    return trim((string) $source[$key]);
}

function community_admin_read_board_list_request(array $get, array $config)
{
    $status = community_admin_read_scalar($get, 'status', '');
    if ($status !== '' && !in_array($status, community_admin_board_status_values(), true)) {
        $status = '';
    }

    $page_rows = isset($config['cf_page_rows']) ? (int) $config['cf_page_rows'] : 15;
    if ($page_rows < 1) {
        $page_rows = 15;
    }

    return array(
        'page' => max(1, (int) community_admin_read_scalar($get, 'page', 1)),
        'stx' => community_admin_read_scalar($get, 'stx', ''),
        'status' => $status,
        'page_rows' => $page_rows,
    );
}

function community_admin_build_board_list_qstr(array $request, array $overrides = array())
{
    $query = array(
        'status' => $request['status'],
        'stx' => $request['stx'],
        'page' => $request['page'],
    );

    foreach ($overrides as $key => $value) {
        $query[$key] = $value;
    }

    foreach ($query as $key => $value) {
        if ($value === '' || $value === null) {
            unset($query[$key]);
        }
    }

    return http_build_query($query);
}

function community_admin_read_board_form_request(array $get)
{
    return array(
        'board_id' => preg_replace('/[^a-z0-9_]/i', '', community_admin_read_scalar($get, 'board_id', '')),
    );
}

function community_admin_read_board_save_request(array $post)
{
    return array(
        'original_board_id' => preg_replace('/[^a-z0-9_]/i', '', community_admin_read_scalar($post, 'original_board_id', '')),
        'board_id' => preg_replace('/[^a-z0-9_]/i', '', community_admin_read_scalar($post, 'board_id', '')),
        'name' => strip_tags(community_admin_read_scalar($post, 'name', '')),
        'description' => strip_tags(community_admin_read_scalar($post, 'description', '')),
        'read_level' => max(1, min(10, (int) community_admin_read_scalar($post, 'read_level', 1))),
        'write_level' => max(1, min(10, (int) community_admin_read_scalar($post, 'write_level', 2))),
        'comment_level' => max(1, min(10, (int) community_admin_read_scalar($post, 'comment_level', 2))),
        'list_order' => (int) community_admin_read_scalar($post, 'list_order', 0),
        'use_category' => isset($post['use_category']) ? 1 : 0,
        'use_latest' => isset($post['use_latest']) ? 1 : 0,
        'use_comment' => isset($post['use_comment']) ? 1 : 0,
        'use_mail_post' => isset($post['use_mail_post']) ? 1 : 0,
        'use_mail_comment' => isset($post['use_mail_comment']) ? 1 : 0,
        'mail_admin' => isset($post['mail_admin']) ? 1 : 0,
        'upload_file_count' => max(0, (int) community_admin_read_scalar($post, 'upload_file_count', 0)),
        'upload_file_size' => max(0, (int) community_admin_read_scalar($post, 'upload_file_size', 0)),
        'upload_extensions' => preg_replace('/[^a-z0-9|,._-]/i', '', community_admin_read_scalar($post, 'upload_extensions', '')),
        'use_point' => isset($post['use_point']) ? 1 : 0,
        'point_write' => (int) community_admin_read_scalar($post, 'point_write', 0),
        'point_comment' => (int) community_admin_read_scalar($post, 'point_comment', 0),
        'point_read' => (int) community_admin_read_scalar($post, 'point_read', 0),
        'status' => community_admin_read_scalar($post, 'status', 'active'),
        'categories' => community_admin_read_scalar($post, 'categories', ''),
    );
}

function community_admin_read_notification_log_request(array $get, array $config)
{
    $page_rows = isset($config['cf_page_rows']) ? (int) $config['cf_page_rows'] : 15;
    if ($page_rows < 1) {
        $page_rows = 15;
    }

    return array(
        'page' => max(1, (int) community_admin_read_scalar($get, 'page', 1)),
        'status' => preg_replace('/[^a-z_]/i', '', community_admin_read_scalar($get, 'status', '')),
        'stx' => community_admin_read_scalar($get, 'stx', ''),
        'page_rows' => $page_rows,
    );
}

function community_admin_build_notification_log_qstr(array $request, array $overrides = array())
{
    $query = array(
        'status' => $request['status'],
        'stx' => $request['stx'],
        'page' => $request['page'],
    );

    foreach ($overrides as $key => $value) {
        $query[$key] = $value;
    }

    foreach ($query as $key => $value) {
        if ($value === '' || $value === null) {
            unset($query[$key]);
        }
    }

    return http_build_query($query);
}

function community_admin_read_notification_log_update_request(array $post)
{
    $action = community_admin_read_scalar($post, 'action', '');
    if (!in_array($action, array('retry'), true)) {
        $action = '';
    }

    return array(
        'action' => $action,
        'notification_ids' => community_admin_read_selected_ids($post, 'notification_id'),
        'return_query' => community_admin_read_scalar($post, 'return_query', ''),
    );
}

function community_admin_post_status_values()
{
    return array('published', 'hidden', 'deleted');
}

function community_admin_read_post_list_request(array $get, array $config)
{
    $status = community_admin_read_scalar($get, 'status', '');
    if ($status !== '' && !in_array($status, community_admin_post_status_values(), true)) {
        $status = '';
    }

    $page_rows = isset($config['cf_page_rows']) ? (int) $config['cf_page_rows'] : 15;
    if ($page_rows < 1) {
        $page_rows = 15;
    }

    return array(
        'page' => max(1, (int) community_admin_read_scalar($get, 'page', 1)),
        'board_id' => preg_replace('/[^a-z0-9_]/i', '', community_admin_read_scalar($get, 'board_id', '')),
        'status' => $status,
        'stx' => community_admin_read_scalar($get, 'stx', ''),
        'page_rows' => $page_rows,
    );
}

function community_admin_build_post_list_qstr(array $request, array $overrides = array())
{
    $query = array(
        'board_id' => $request['board_id'],
        'status' => $request['status'],
        'stx' => $request['stx'],
        'page' => $request['page'],
    );

    foreach ($overrides as $key => $value) {
        $query[$key] = $value;
    }

    foreach ($query as $key => $value) {
        if ($value === '' || $value === null) {
            unset($query[$key]);
        }
    }

    return http_build_query($query);
}

function community_admin_read_selected_ids(array $post, $key)
{
    $ids = array();
    if (!isset($post[$key]) || !is_array($post[$key])) {
        return $ids;
    }

    foreach ($post[$key] as $id) {
        $id = (int) $id;
        if ($id > 0) {
            $ids[] = $id;
        }
    }

    return array_values(array_unique($ids));
}

function community_admin_read_post_list_update_request(array $post)
{
    $action = community_admin_read_scalar($post, 'action', '');
    if (!in_array($action, array('publish', 'hide', 'delete', 'notice_on', 'notice_off'), true)) {
        $action = '';
    }

    return array(
        'action' => $action,
        'post_ids' => community_admin_read_selected_ids($post, 'post_id'),
        'return_query' => community_admin_read_scalar($post, 'return_query', ''),
    );
}

function community_admin_read_comment_list_request(array $get, array $config)
{
    $status = community_admin_read_scalar($get, 'status', '');
    if ($status !== '' && !in_array($status, community_admin_post_status_values(), true)) {
        $status = '';
    }

    $page_rows = isset($config['cf_page_rows']) ? (int) $config['cf_page_rows'] : 15;
    if ($page_rows < 1) {
        $page_rows = 15;
    }

    return array(
        'page' => max(1, (int) community_admin_read_scalar($get, 'page', 1)),
        'post_id' => max(0, (int) community_admin_read_scalar($get, 'post_id', 0)),
        'status' => $status,
        'stx' => community_admin_read_scalar($get, 'stx', ''),
        'page_rows' => $page_rows,
    );
}

function community_admin_build_comment_list_qstr(array $request, array $overrides = array())
{
    $query = array(
        'post_id' => $request['post_id'],
        'status' => $request['status'],
        'stx' => $request['stx'],
        'page' => $request['page'],
    );

    foreach ($overrides as $key => $value) {
        $query[$key] = $value;
    }

    foreach ($query as $key => $value) {
        if ($value === '' || $value === null || $value === 0) {
            unset($query[$key]);
        }
    }

    return http_build_query($query);
}

function community_admin_read_comment_list_update_request(array $post)
{
    $action = community_admin_read_scalar($post, 'action', '');
    if (!in_array($action, array('publish', 'hide', 'delete'), true)) {
        $action = '';
    }

    return array(
        'action' => $action,
        'comment_ids' => community_admin_read_selected_ids($post, 'comment_id'),
        'return_query' => community_admin_read_scalar($post, 'return_query', ''),
    );
}

function community_admin_read_point_list_request(array $get, array $config)
{
    $page_rows = isset($config['cf_page_rows']) ? (int) $config['cf_page_rows'] : 15;
    if ($page_rows < 1) {
        $page_rows = 15;
    }

    return array(
        'page' => max(1, (int) community_admin_read_scalar($get, 'page', 1)),
        'mb_id' => preg_replace('/[^a-z0-9_]/i', '', community_admin_read_scalar($get, 'mb_id', '')),
        'page_rows' => $page_rows,
    );
}

function community_admin_build_point_list_qstr(array $request, array $overrides = array())
{
    $query = array(
        'mb_id' => $request['mb_id'],
        'page' => $request['page'],
    );

    foreach ($overrides as $key => $value) {
        $query[$key] = $value;
    }

    foreach ($query as $key => $value) {
        if ($value === '' || $value === null) {
            unset($query[$key]);
        }
    }

    return http_build_query($query);
}

function community_admin_read_point_adjust_request(array $post)
{
    return array(
        'mb_id' => preg_replace('/[^a-z0-9_]/i', '', community_admin_read_scalar($post, 'mb_id', '')),
        'amount' => (int) community_admin_read_scalar($post, 'amount', 0),
        'memo' => strip_tags(community_admin_read_scalar($post, 'memo', '')),
        'return_query' => community_admin_read_scalar($post, 'return_query', ''),
    );
}

function community_admin_read_point_expire_request(array $post)
{
    return array(
        'return_query' => community_admin_read_scalar($post, 'return_query', ''),
    );
}
