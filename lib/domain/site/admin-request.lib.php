<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function site_admin_read_scalar(array $source, $key, $default = '')
{
    if (!array_key_exists($key, $source) || is_array($source[$key])) {
        return $default;
    }

    return trim((string) $source[$key]);
}

function site_admin_page_status_values()
{
    return array('active', 'hidden');
}

function site_admin_page_format_values()
{
    return array('html', 'text');
}

function site_admin_read_page_list_request(array $get, array $config)
{
    $content_format = site_admin_read_scalar($get, 'content_format', '');
    if ($content_format !== '' && !in_array($content_format, site_admin_page_format_values(), true)) {
        $content_format = '';
    }

    $status = site_admin_read_scalar($get, 'status', '');
    if ($status !== '' && !in_array($status, site_admin_page_status_values(), true)) {
        $status = '';
    }

    $page_rows = isset($config['cf_page_rows']) ? (int) $config['cf_page_rows'] : 15;
    if ($page_rows < 1) {
        $page_rows = 15;
    }

    return array(
        'page' => max(1, (int) site_admin_read_scalar($get, 'page', 1)),
        'content_format' => $content_format,
        'status' => $status,
        'stx' => site_admin_read_scalar($get, 'stx', ''),
        'page_rows' => $page_rows,
    );
}

function site_admin_build_page_list_qstr(array $request, array $overrides = array())
{
    $query = array(
        'content_format' => $request['content_format'],
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

function site_admin_read_page_form_request(array $get)
{
    return array(
        'page_id' => max(0, (int) site_admin_read_scalar($get, 'page_id', 0)),
    );
}

function site_admin_read_page_save_request(array $post)
{
    $content_format = site_admin_read_scalar($post, 'content_format', 'html');
    if (!in_array($content_format, site_admin_page_format_values(), true)) {
        $content_format = 'html';
    }

    $status = site_admin_read_scalar($post, 'status', 'active');
    if (!in_array($status, site_admin_page_status_values(), true)) {
        $status = 'active';
    }

    return array(
        'page_id' => max(0, (int) site_admin_read_scalar($post, 'page_id', 0)),
        'slug' => site_normalize_page_slug(site_admin_read_scalar($post, 'slug', '')),
        'title' => strip_tags(site_admin_read_scalar($post, 'title', '')),
        'summary' => strip_tags(site_admin_read_scalar($post, 'summary', '')),
        'content' => (string) site_admin_read_scalar($post, 'content', ''),
        'content_format' => $content_format,
        'access_level' => max(1, min(10, (int) site_admin_read_scalar($post, 'access_level', 1))),
        'show_pc' => isset($post['show_pc']) ? 1 : 0,
        'show_mobile' => isset($post['show_mobile']) ? 1 : 0,
        'list_order' => (int) site_admin_read_scalar($post, 'list_order', 0),
        'status' => $status,
    );
}
