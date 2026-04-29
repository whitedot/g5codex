<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_config_table()
{
    global $g5;

    return $g5['community_config_table'];
}

function community_config_defaults()
{
    return array(
        'point_expire_days' => 0,
        'board_read_level' => 1,
        'board_write_level' => 2,
        'board_comment_level' => 2,
        'board_use_category' => 0,
        'board_use_latest' => 1,
        'board_use_comment' => 1,
        'board_use_mail_post' => 1,
        'board_use_mail_comment' => 1,
        'board_mail_admin' => 0,
        'board_upload_file_count' => 0,
        'board_upload_file_size' => 0,
        'board_upload_extensions' => '',
        'board_use_point' => 0,
        'board_point_write' => 0,
        'board_point_comment' => 0,
        'board_point_read' => 0,
    );
}

function community_config_ensure_table()
{
    $table = community_config_table();
    if (sql_table_exists($table)) {
        return true;
    }

    return (bool) sql_query(
        " create table if not exists {$table} (
            config_key varchar(100) not null default '',
            config_value text not null,
            updated_at datetime not null default '0000-00-00 00:00:00',
            primary key (config_key)
        ) engine=MyISAM default charset=utf8 ",
        false
    );
}

function community_get_config()
{
    $config = community_config_defaults();
    if (!community_config_ensure_table()) {
        return $config;
    }

    $table = community_config_table();
    $rows = sql_fetch_all_prepared(" select config_key, config_value from {$table} ", array());
    foreach ($rows as $row) {
        if (array_key_exists($row['config_key'], $config)) {
            $config[$row['config_key']] = $row['config_value'];
        }
    }

    foreach ($config as $key => $value) {
        if ($key === 'board_upload_extensions') {
            $config[$key] = (string) $value;
        } else {
            $config[$key] = (int) $value;
        }
    }

    return $config;
}

function community_get_config_value($key, $default = null)
{
    $config = community_get_config();

    return array_key_exists($key, $config) ? $config[$key] : $default;
}

function community_set_config_values(array $values)
{
    $defaults = community_config_defaults();
    if (!community_config_ensure_table()) {
        return false;
    }

    $table = community_config_table();
    foreach ($values as $key => $value) {
        if (!array_key_exists($key, $defaults)) {
            continue;
        }

        if (!sql_query_prepared(
            " insert into {$table}
                set config_key = :config_key,
                    config_value = :config_value,
                    updated_at = :updated_at
              on duplicate key update
                    config_value = values(config_value),
                    updated_at = values(updated_at) ",
            array(
                'config_key' => $key,
                'config_value' => (string) $value,
                'updated_at' => G5_TIME_YMDHIS,
            ),
            false
        )) {
            return false;
        }
    }

    return true;
}

function community_point_expire_days()
{
    return max(0, (int) community_get_config_value('point_expire_days', 0));
}

function community_point_calculate_expires_at($base_time = '')
{
    $days = community_point_expire_days();
    if ($days < 1) {
        return '0000-00-00 00:00:00';
    }

    $base_timestamp = $base_time !== '' ? strtotime($base_time) : (defined('G5_SERVER_TIME') ? G5_SERVER_TIME : time());
    if ($base_timestamp === false) {
        $base_timestamp = defined('G5_SERVER_TIME') ? G5_SERVER_TIME : time();
    }

    return date('Y-m-d H:i:s', strtotime('+' . $days . ' days', $base_timestamp));
}
