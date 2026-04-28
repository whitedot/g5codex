<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_board_table()
{
    global $g5;

    return $g5['community_board_table'];
}

function community_post_table()
{
    global $g5;

    return $g5['community_post_table'];
}

function community_payload_value(array $payload, $key, $default = '')
{
    return array_key_exists($key, $payload) ? $payload[$key] : $default;
}

function community_fetch_board($board_id, $include_hidden = false)
{
    $table = community_board_table();
    $params = array('board_id' => $board_id);
    $status_sql = '';

    if (!$include_hidden) {
        $status_sql = " and status = 'active' ";
    }

    return sql_fetch_prepared(" select * from {$table} where board_id = :board_id {$status_sql} ", $params);
}

function community_fetch_board_list($include_hidden = false)
{
    $table = community_board_table();
    $status_sql = $include_hidden ? '' : " where status = 'active' ";

    return sql_fetch_all_prepared(
        " select * from {$table} {$status_sql} order by list_order asc, board_id asc ",
        array()
    );
}

function community_fetch_board_categories($board_id)
{
    global $g5;

    $table = $g5['community_board_category_table'];

    return sql_fetch_all_prepared(
        " select * from {$table}
          where board_id = :board_id and status = 'active'
          order by list_order asc, category_id asc ",
        array('board_id' => $board_id)
    );
}

function community_fetch_board_category($board_id, $category_id)
{
    global $g5;

    $table = $g5['community_board_category_table'];

    return sql_fetch_prepared(
        " select * from {$table}
          where board_id = :board_id and category_id = :category_id and status = 'active' ",
        array(
            'board_id' => $board_id,
            'category_id' => (int) $category_id,
        )
    );
}

function community_normalize_post_list_request(array $request)
{
    $page = max(1, (int) community_payload_value($request, 'page', 1));
    $page_rows = (int) community_payload_value($request, 'page_rows', 15);

    if ($page_rows < 1) {
        $page_rows = 15;
    }

    return array(
        'page' => $page,
        'page_rows' => min(100, $page_rows),
        'category_id' => max(0, (int) community_payload_value($request, 'category_id', 0)),
        'status' => preg_replace('/[^a-z_]/i', '', (string) community_payload_value($request, 'status', 'published')),
    );
}

function community_build_post_list_sql($board_id, array $request, array &$params)
{
    $where = array('board_id = :board_id');
    $params['board_id'] = $board_id;

    if ($request['status'] !== '') {
        $where[] = 'status = :status';
        $params['status'] = $request['status'];
    }

    if ($request['category_id'] > 0) {
        $where[] = 'category_id = :category_id';
        $params['category_id'] = $request['category_id'];
    }

    return ' where ' . implode(' and ', $where);
}

function community_fetch_post($post_id, $include_deleted = false)
{
    $table = community_post_table();
    $params = array('post_id' => (int) $post_id);
    $status_sql = $include_deleted ? '' : " and status <> 'deleted' ";

    return sql_fetch_prepared(" select * from {$table} where post_id = :post_id {$status_sql} ", $params);
}

function community_fetch_post_in_board($board_id, $post_id, $include_deleted = false)
{
    $table = community_post_table();
    $status_sql = $include_deleted ? '' : " and status <> 'deleted' ";

    return sql_fetch_prepared(
        " select * from {$table}
          where board_id = :board_id and post_id = :post_id {$status_sql} ",
        array(
            'board_id' => $board_id,
            'post_id' => (int) $post_id,
        )
    );
}

function community_increment_post_view_count($post_id)
{
    $table = community_post_table();

    return (bool) sql_query_prepared(
        " update {$table}
             set view_count = view_count + 1
           where post_id = :post_id
             and status = 'published' ",
        array('post_id' => (int) $post_id),
        false
    );
}

function community_mark_post_viewed(array $post)
{
    if (empty($post['post_id'])) {
        return false;
    }

    $post_id = (int) $post['post_id'];
    $cookie_name = 'community_view_' . $post_id;

    if (get_cookie($cookie_name) === '1') {
        return false;
    }

    if (!community_increment_post_view_count($post_id)) {
        return false;
    }

    set_cookie($cookie_name, '1', 86400);
    return true;
}

function community_fetch_adjacent_post($board_id, array $post, $direction = 'prev')
{
    $table = community_post_table();
    $operator = $direction === 'next' ? '>' : '<';
    $order = $direction === 'next' ? 'asc' : 'desc';
    $params = array(
        'board_id' => $board_id,
        'post_id' => (int) $post['post_id'],
    );
    $category_sql = '';

    if ((int) $post['category_id'] > 0) {
        $category_sql = ' and category_id = :category_id ';
        $params['category_id'] = (int) $post['category_id'];
    }

    return sql_fetch_prepared(
        " select * from {$table}
          where board_id = :board_id
            and status = 'published'
            {$category_sql}
            and post_id {$operator} :post_id
          order by post_id {$order}
          limit 1 ",
        $params
    );
}

function community_fetch_post_list_page($board_id, array $request = array())
{
    $table = community_post_table();
    $request = community_normalize_post_list_request($request);
    $params = array();
    $where = community_build_post_list_sql($board_id, $request, $params);
    $count_row = sql_fetch_prepared(" select count(*) as cnt from {$table} {$where} ", $params);
    $total_count = isset($count_row['cnt']) ? (int) $count_row['cnt'] : 0;
    $from_record = ($request['page'] - 1) * $request['page_rows'];

    $list_params = $params;
    $list_params['from_record'] = $from_record;
    $list_params['page_rows'] = $request['page_rows'];

    $rows = sql_fetch_all_prepared(
        " select * from {$table} {$where}
          order by is_notice desc, notice_order asc, last_activity_at desc, post_id desc
          limit :from_record, :page_rows ",
        $list_params
    );

    return array(
        'total_count' => $total_count,
        'rows' => $rows,
        'from_record' => $from_record,
        'request' => $request,
    );
}

function community_insert_post(array $payload)
{
    $table = community_post_table();
    $now = G5_TIME_YMDHIS;
    $params = array(
        'board_id' => preg_replace('/[^a-z0-9_]/i', '', (string) community_payload_value($payload, 'board_id', '')),
        'category_id' => max(0, (int) community_payload_value($payload, 'category_id', 0)),
        'mb_id' => (string) community_payload_value($payload, 'mb_id', ''),
        'title' => strip_tags((string) community_payload_value($payload, 'title', '')),
        'content' => (string) community_payload_value($payload, 'content', ''),
        'content_format' => preg_replace('/[^a-z0-9_]/i', '', (string) community_payload_value($payload, 'content_format', 'text')),
        'summary' => strip_tags((string) community_payload_value($payload, 'summary', '')),
        'is_notice' => !empty($payload['is_notice']) ? 1 : 0,
        'notice_order' => (int) community_payload_value($payload, 'notice_order', 0),
        'notice_started_at' => (string) community_payload_value($payload, 'notice_started_at', '0000-00-00 00:00:00'),
        'notice_ended_at' => (string) community_payload_value($payload, 'notice_ended_at', '0000-00-00 00:00:00'),
        'is_secret' => !empty($payload['is_secret']) ? 1 : 0,
        'status' => preg_replace('/[^a-z_]/i', '', (string) community_payload_value($payload, 'status', 'published')),
        'created_at' => $now,
        'updated_at' => $now,
        'last_activity_at' => $now,
    );

    if ($params['board_id'] === '' || $params['title'] === '') {
        return 0;
    }

    $sql = " insert into {$table}
                set board_id = :board_id,
                    category_id = :category_id,
                    mb_id = :mb_id,
                    title = :title,
                    content = :content,
                    content_format = :content_format,
                    summary = :summary,
                    is_notice = :is_notice,
                    notice_order = :notice_order,
                    notice_started_at = :notice_started_at,
                    notice_ended_at = :notice_ended_at,
                    is_secret = :is_secret,
                    status = :status,
                    created_at = :created_at,
                    updated_at = :updated_at,
                    last_activity_at = :last_activity_at ";

    if (!sql_query_prepared($sql, $params, false)) {
        return 0;
    }

    return sql_insert_id();
}

function community_update_post($post_id, array $payload)
{
    $table = community_post_table();
    $params = array(
        'post_id' => (int) $post_id,
        'category_id' => max(0, (int) community_payload_value($payload, 'category_id', 0)),
        'title' => strip_tags((string) community_payload_value($payload, 'title', '')),
        'content' => (string) community_payload_value($payload, 'content', ''),
        'content_format' => preg_replace('/[^a-z0-9_]/i', '', (string) community_payload_value($payload, 'content_format', 'text')),
        'summary' => strip_tags((string) community_payload_value($payload, 'summary', '')),
        'is_notice' => !empty($payload['is_notice']) ? 1 : 0,
        'notice_order' => (int) community_payload_value($payload, 'notice_order', 0),
        'notice_started_at' => (string) community_payload_value($payload, 'notice_started_at', '0000-00-00 00:00:00'),
        'notice_ended_at' => (string) community_payload_value($payload, 'notice_ended_at', '0000-00-00 00:00:00'),
        'is_secret' => !empty($payload['is_secret']) ? 1 : 0,
        'updated_at' => G5_TIME_YMDHIS,
    );

    if ($params['post_id'] < 1 || $params['title'] === '') {
        return false;
    }

    $sql = " update {$table}
                set category_id = :category_id,
                    title = :title,
                    content = :content,
                    content_format = :content_format,
                    summary = :summary,
                    is_notice = :is_notice,
                    notice_order = :notice_order,
                    notice_started_at = :notice_started_at,
                    notice_ended_at = :notice_ended_at,
                    is_secret = :is_secret,
                    updated_at = :updated_at
              where post_id = :post_id and status <> 'deleted' ";

    return (bool) sql_query_prepared($sql, $params, false);
}

function community_soft_delete_post($post_id)
{
    $table = community_post_table();

    return (bool) sql_query_prepared(
        " update {$table}
             set status = 'deleted',
                 updated_at = :updated_at,
                 deleted_at = :deleted_at
           where post_id = :post_id and status <> 'deleted' ",
        array(
            'post_id' => (int) $post_id,
            'updated_at' => G5_TIME_YMDHIS,
            'deleted_at' => G5_TIME_YMDHIS,
        ),
        false
    );
}
