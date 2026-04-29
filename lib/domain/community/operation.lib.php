<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_board_group_table()
{
    global $g5;

    return $g5['community_board_group_table'];
}

function site_menu_table()
{
    global $g5;

    return $g5['site_menu_table'];
}

function site_banner_table()
{
    global $g5;

    return $g5['site_banner_table'];
}

function site_banner_default_position_groups()
{
    return array(
        'common' => array(
            'label' => '공통',
            'positions' => array(
                'main_top' => '메인 상단',
                'main_middle' => '메인 중단',
                'side' => '사이드',
            ),
        ),
        'community' => array(
            'label' => '커뮤니티',
            'positions' => array(
                'community_top' => '커뮤니티 상단',
                'board_list_top' => '게시판 목록 상단',
                'post_view_bottom' => '게시글 보기 하단',
            ),
        ),
    );
}

function site_banner_normalize_position_groups(array $groups)
{
    $normalized = array();

    foreach ($groups as $group_key => $group) {
        $group_key = preg_replace('/[^a-z0-9_]/i', '', (string) $group_key);
        if ($group_key === '' || !is_array($group)) {
            continue;
        }

        $label = isset($group['label']) ? (string) $group['label'] : $group_key;
        $positions = isset($group['positions']) && is_array($group['positions']) ? $group['positions'] : $group;
        unset($positions['label'], $positions['positions']);

        foreach ($positions as $position => $position_label) {
            $position = preg_replace('/[^a-z0-9_]/i', '', (string) $position);
            if ($position === '') {
                continue;
            }

            if (is_array($position_label)) {
                $position_label = isset($position_label['label']) ? $position_label['label'] : $position;
            }

            $normalized[$group_key]['label'] = $label;
            $normalized[$group_key]['positions'][$position] = (string) $position_label;
        }
    }

    return $normalized;
}

function site_banner_position_groups()
{
    $groups = site_banner_default_position_groups();
    $replaced = function_exists('run_replace') ? run_replace('site_banner_position_groups', $groups) : null;
    if (is_array($replaced)) {
        $groups = $replaced;
    }

    return site_banner_normalize_position_groups($groups);
}

function site_banner_position_map()
{
    $map = array();
    foreach (site_banner_position_groups() as $group) {
        foreach ($group['positions'] as $position => $label) {
            $map[$position] = $label;
        }
    }

    return $map;
}

function site_banner_position_values()
{
    return array_keys(site_banner_position_map());
}

function site_banner_default_position()
{
    $positions = site_banner_position_values();

    return isset($positions[0]) ? $positions[0] : 'main_top';
}

function site_banner_position_label($position)
{
    $map = site_banner_position_map();

    return isset($map[$position]) ? $map[$position] : $position;
}

function community_schema_column_exists($table, $column)
{
    $table = preg_replace('/[^a-z0-9_]/i', '', $table);
    $column = sql_escape_string($column);
    if ($table === '' || $column === '') {
        return false;
    }

    $row = sql_fetch(" show columns from {$table} like '{$column}' ", false);

    return !empty($row['Field']);
}

function community_ensure_operation_schema()
{
    global $g5;

    $group_table = community_board_group_table();
    if (!sql_table_exists($group_table)) {
        sql_query(
            " create table if not exists {$group_table} (
                group_id varchar(50) not null default '',
                name varchar(255) not null default '',
                description text not null,
                read_level tinyint(4) not null default '1',
                write_level tinyint(4) not null default '2',
                comment_level tinyint(4) not null default '2',
                list_order int(11) not null default '0',
                status varchar(20) not null default 'active',
                created_at datetime not null default '0000-00-00 00:00:00',
                updated_at datetime not null default '0000-00-00 00:00:00',
                primary key (group_id),
                key idx_status_order (status, list_order)
            ) engine=MyISAM default charset=utf8 ",
            false
        );
    }

    if (!empty($g5['community_board_table']) && sql_table_exists($g5['community_board_table']) && !community_schema_column_exists($g5['community_board_table'], 'group_id')) {
        sql_query(" alter table {$g5['community_board_table']} add group_id varchar(50) not null default '' after board_id, add key idx_group_order (group_id, status, list_order) ", false);
    }

    $menu_table = site_menu_table();
    if (!sql_table_exists($menu_table)) {
        sql_query(
            " create table if not exists {$menu_table} (
                menu_id bigint(20) unsigned not null auto_increment,
                parent_id bigint(20) unsigned not null default '0',
                menu_type varchar(20) not null default 'url',
                target_id varchar(100) not null default '',
                name varchar(255) not null default '',
                url varchar(255) not null default '',
                target_blank tinyint(1) not null default '0',
                access_level tinyint(4) not null default '1',
                show_pc tinyint(1) not null default '1',
                show_mobile tinyint(1) not null default '1',
                list_order int(11) not null default '0',
                status varchar(20) not null default 'active',
                created_at datetime not null default '0000-00-00 00:00:00',
                updated_at datetime not null default '0000-00-00 00:00:00',
                primary key (menu_id),
                key idx_parent_order (parent_id, status, list_order),
                key idx_target (menu_type, target_id)
            ) engine=MyISAM default charset=utf8 ",
            false
        );
    }

    $banner_table = site_banner_table();
    if (!sql_table_exists($banner_table)) {
        sql_query(
            " create table if not exists {$banner_table} (
                banner_id bigint(20) unsigned not null auto_increment,
                position varchar(50) not null default '',
                title varchar(255) not null default '',
                image_path varchar(255) not null default '',
                mobile_image_path varchar(255) not null default '',
                link_url varchar(255) not null default '',
                target_blank tinyint(1) not null default '0',
                started_at datetime not null default '0000-00-00 00:00:00',
                ended_at datetime not null default '0000-00-00 00:00:00',
                show_pc tinyint(1) not null default '1',
                show_mobile tinyint(1) not null default '1',
                list_order int(11) not null default '0',
                status varchar(20) not null default 'active',
                created_at datetime not null default '0000-00-00 00:00:00',
                updated_at datetime not null default '0000-00-00 00:00:00',
                primary key (banner_id),
                key idx_position_period (position, status, started_at, ended_at, list_order)
            ) engine=MyISAM default charset=utf8 ",
            false
        );
    }
}

function community_fetch_board_group_list($include_hidden = false)
{
    community_ensure_operation_schema();
    $table = community_board_group_table();
    $status_sql = $include_hidden ? '' : " where status = 'active' ";

    return sql_fetch_all_prepared(
        " select * from {$table} {$status_sql} order by list_order asc, group_id asc ",
        array()
    );
}

function site_build_menu_url(array $row)
{
    if ($row['menu_type'] === 'disabled') {
        return '#';
    }

    if ($row['menu_type'] === 'board_group' && $row['target_id'] !== '') {
        return G5_COMMUNITY_URL . '/index.php?group_id=' . rawurlencode($row['target_id']);
    }

    if ($row['menu_type'] === 'page' && $row['target_id'] !== '' && function_exists('site_page_url')) {
        return site_page_url($row['target_id']);
    }

    if ($row['menu_type'] === 'board' && $row['target_id'] !== '') {
        return G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($row['target_id']);
    }

    return $row['url'];
}

function site_fetch_menu_tree($device = '', array $viewer = array())
{
    global $member;

    if (empty($viewer) && is_array($member)) {
        $viewer = $member;
    }

    $viewer_level = isset($viewer['mb_level']) ? (int) $viewer['mb_level'] : 1;
    community_ensure_operation_schema();
    $table = site_menu_table();
    $where = " where status = 'active' and access_level <= :access_level ";
    $params = array('access_level' => $viewer_level);
    if ($device === 'pc') {
        $where .= ' and show_pc = 1 ';
    } elseif ($device === 'mobile') {
        $where .= ' and show_mobile = 1 ';
    }

    $rows = sql_fetch_all_prepared(
        " select * from {$table} {$where} order by parent_id asc, list_order asc, menu_id asc ",
        $params
    );

    $items = array();
    foreach ($rows as $row) {
        $row['url'] = site_build_menu_url($row);
        $row['children'] = array();
        $items[(int) $row['menu_id']] = $row;
    }

    foreach (array_keys($items) as $menu_id) {
        $parent_id = (int) $items[$menu_id]['parent_id'];
        if ($parent_id > 0 && isset($items[$parent_id])) {
            $items[$parent_id]['children'][] = $items[$menu_id];
        }
    }

    $tree = array();
    foreach ($items as $row) {
        $parent_id = (int) $row['parent_id'];
        if ($parent_id === 0 || !isset($items[$parent_id])) {
            $tree[] = $row;
        }
    }

    return $tree;
}

function site_banner_image_url($path)
{
    $path = ltrim((string) $path, '/');

    return $path !== '' ? G5_DATA_URL . '/' . $path : '';
}

function site_fetch_banners($position, $device = '')
{
    community_ensure_operation_schema();
    $table = site_banner_table();
    $where = " where position = :position
                 and status = 'active'
                 and (started_at = '0000-00-00 00:00:00' or started_at <= :now)
                 and (ended_at = '0000-00-00 00:00:00' or ended_at >= :now) ";
    if ($device === 'pc') {
        $where .= ' and show_pc = 1 ';
    } elseif ($device === 'mobile') {
        $where .= ' and show_mobile = 1 ';
    }

    $rows = sql_fetch_all_prepared(
        " select * from {$table} {$where} order by list_order asc, banner_id desc ",
        array(
            'position' => $position,
            'now' => G5_TIME_YMDHIS,
        )
    );

    foreach ($rows as $index => $row) {
        $rows[$index]['image_url'] = site_banner_image_url($row['image_path']);
        $rows[$index]['mobile_image_url'] = site_banner_image_url($row['mobile_image_path']);
    }

    return $rows;
}
