<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function admin_build_select_option_view($value, $label, $selected = false)
{
    return array(
        'value_attr' => admin_escape_attr($value),
        'label_text' => get_text((string) $label),
        'selected_attr' => $selected ? ' selected' : '',
    );
}

function admin_format_count_text($count, $suffix = '')
{
    return number_format((int) $count) . (string) $suffix;
}

function admin_build_hidden_field_views(array $fields)
{
    $views = array();

    foreach ($fields as $name => $value) {
        $views[] = array(
            'name_attr' => admin_escape_attr($name),
            'value_attr' => get_sanitize_input($value),
        );
    }

    return $views;
}

function admin_escape_attr($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function admin_json_string($value)
{
    return json_encode((string) $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function admin_build_member_level_options($start_id = 0, $end_id = 10, $selected = '')
{
    $options = array();

    for ($i = $start_id; $i <= $end_id; $i++) {
        $options[] = admin_build_select_option_view($i, $i, ((string) $i === (string) $selected));
    }

    return $options;
}

function admin_read_member_id_options($level, $selected = '')
{
    global $g5;

    $options = array(
        admin_build_select_option_view('', '선택안함', ((string) $selected === '')),
    );

    $sql = " select mb_id from {$g5['member_table']} where mb_level >= :mb_level ";
    $result = sql_query_prepared($sql, array(
        'mb_level' => (int) $level,
    ));

    for ($i = 0; $row = sql_fetch_array($result); $i++) {
        $mb_id = (string) $row['mb_id'];
        $options[] = admin_build_select_option_view($mb_id, $mb_id, ($mb_id === (string) $selected));
    }

    return $options;
}
