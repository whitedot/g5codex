<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function site_page_table()
{
    global $g5;

    return $g5['site_page_table'];
}

function site_page_url($slug)
{
    return G5_URL . '/page.php?slug=' . rawurlencode($slug);
}

function site_ensure_page_schema()
{
    $table = site_page_table();
    if (sql_table_exists($table)) {
        return;
    }

    sql_query(
        " create table if not exists {$table} (
            page_id bigint(20) unsigned not null auto_increment,
            slug varchar(100) not null default '',
            title varchar(255) not null default '',
            summary varchar(255) not null default '',
            content mediumtext not null,
            content_format varchar(20) not null default 'html',
            access_level tinyint(4) not null default '1',
            show_pc tinyint(1) not null default '1',
            show_mobile tinyint(1) not null default '1',
            list_order int(11) not null default '0',
            status varchar(20) not null default 'active',
            created_at datetime not null default '0000-00-00 00:00:00',
            updated_at datetime not null default '0000-00-00 00:00:00',
            primary key (page_id),
            unique key uq_slug (slug),
            key idx_status_order (status, list_order, page_id),
            key idx_access_device (status, access_level, show_pc, show_mobile)
        ) engine=MyISAM default charset=utf8 ",
        false
    );
}

function site_normalize_page_slug($slug)
{
    $slug = strtolower(trim((string) $slug));
    $slug = preg_replace('/[^a-z0-9_-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);

    return trim($slug, '-');
}

function site_fetch_page_by_slug($slug, $device = '', array $viewer = array())
{
    global $member;

    $slug = site_normalize_page_slug($slug);
    if ($slug === '') {
        return array();
    }

    if (empty($viewer) && is_array($member)) {
        $viewer = $member;
    }

    $viewer_level = isset($viewer['mb_level']) ? (int) $viewer['mb_level'] : 1;
    site_ensure_page_schema();
    $table = site_page_table();
    $where = " where slug = :slug and status = 'active' and access_level <= :access_level ";
    $params = array(
        'slug' => $slug,
        'access_level' => $viewer_level,
    );

    if ($device === 'pc') {
        $where .= ' and show_pc = 1 ';
    } elseif ($device === 'mobile') {
        $where .= ' and show_mobile = 1 ';
    }

    return sql_fetch_prepared(" select * from {$table} {$where} limit 1 ", $params);
}

function site_render_page_content(array $page)
{
    if ($page['content_format'] === 'text') {
        return nl2br(get_text($page['content']));
    }

    return conv_content($page['content'], 1);
}
