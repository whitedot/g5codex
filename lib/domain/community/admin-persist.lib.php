<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_admin_board_table()
{
    global $g5;

    return $g5['community_board_table'];
}

function community_admin_board_category_table()
{
    global $g5;

    return $g5['community_board_category_table'];
}

function community_admin_fetch_board($board_id)
{
    $table = community_admin_board_table();

    return sql_fetch_prepared(" select * from {$table} where board_id = :board_id ", array(
        'board_id' => $board_id,
    ));
}

function community_admin_fetch_board_categories($board_id)
{
    $table = community_admin_board_category_table();

    return sql_fetch_all_prepared(
        " select * from {$table} where board_id = :board_id and status = 'active' order by list_order asc, category_id asc ",
        array('board_id' => $board_id)
    );
}

function community_admin_build_board_search_sql(array $request, array &$params)
{
    $where = array('1=1');

    if ($request['status'] !== '') {
        $where[] = 'status = :status';
        $params['status'] = $request['status'];
    }

    if ($request['stx'] !== '') {
        $where[] = '(board_id like :stx_like or name like :stx_like)';
        $params['stx_like'] = '%' . $request['stx'] . '%';
    }

    return ' where ' . implode(' and ', $where);
}

function community_admin_fetch_board_list_page(array $request)
{
    $table = community_admin_board_table();
    $params = array();
    $where = community_admin_build_board_search_sql($request, $params);
    $count_row = sql_fetch_prepared(" select count(*) as cnt from {$table} {$where} ", $params);
    $total_count = isset($count_row['cnt']) ? (int) $count_row['cnt'] : 0;
    $from_record = ($request['page'] - 1) * $request['page_rows'];

    $list_params = $params;
    $list_params['from_record'] = $from_record;
    $list_params['page_rows'] = $request['page_rows'];

    $rows = sql_fetch_all_prepared(
        " select * from {$table} {$where} order by list_order asc, board_id asc limit :from_record, :page_rows ",
        $list_params
    );

    return array(
        'total_count' => $total_count,
        'rows' => $rows,
        'from_record' => $from_record,
    );
}

function community_admin_normalize_category_names($category_text)
{
    $lines = preg_split('/\r\n|\r|\n/', (string) $category_text);
    $names = array();

    foreach ($lines as $line) {
        $name = trim(strip_tags($line));
        if ($name === '') {
            continue;
        }
        if (!in_array($name, $names, true)) {
            $names[] = $name;
        }
    }

    return $names;
}

function community_admin_validate_board_request(array $request, $is_update)
{
    if ($request['board_id'] === '') {
        return '게시판 ID를 입력하세요.';
    }

    if (!preg_match('/^[a-z][a-z0-9_]{1,49}$/i', $request['board_id'])) {
        return '게시판 ID는 영문자로 시작하고 영문자, 숫자, _ 만 사용할 수 있습니다.';
    }

    if ($request['name'] === '') {
        return '게시판 이름을 입력하세요.';
    }

    if (!in_array($request['status'], community_admin_board_status_values(), true)) {
        return '게시판 상태가 올바르지 않습니다.';
    }

    if ($is_update && $request['original_board_id'] !== $request['board_id']) {
        return '게시판 ID는 수정할 수 없습니다.';
    }

    $existing = community_admin_fetch_board($request['board_id']);
    if (!$is_update && isset($existing['board_id']) && $existing['board_id'] !== '') {
        return '이미 존재하는 게시판 ID입니다.';
    }

    return '';
}

function community_admin_save_board_categories($board_id, $category_text)
{
    $table = community_admin_board_category_table();
    $names = community_admin_normalize_category_names($category_text);

    sql_query_prepared(
        " update {$table} set status = 'deleted', updated_at = :updated_at where board_id = :board_id ",
        array('board_id' => $board_id, 'updated_at' => G5_TIME_YMDHIS)
    );

    foreach ($names as $index => $name) {
        sql_query_prepared(
            " insert into {$table}
                set board_id = :board_id,
                    name = :name,
                    list_order = :list_order,
                    status = 'active',
                    created_at = :created_at,
                    updated_at = :updated_at
              on duplicate key update
                    list_order = values(list_order),
                    status = 'active',
                    updated_at = values(updated_at) ",
            array(
                'board_id' => $board_id,
                'name' => $name,
                'list_order' => $index + 1,
                'created_at' => G5_TIME_YMDHIS,
                'updated_at' => G5_TIME_YMDHIS,
            )
        );
    }
}

function community_admin_save_board(array $request)
{
    $is_update = ($request['original_board_id'] !== '');
    $error = community_admin_validate_board_request($request, $is_update);

    if ($error !== '') {
        return array('error' => $error, 'board_id' => $request['board_id']);
    }

    $table = community_admin_board_table();
    $params = array(
        'name' => $request['name'],
        'description' => $request['description'],
        'read_level' => $request['read_level'],
        'write_level' => $request['write_level'],
        'comment_level' => $request['comment_level'],
        'list_order' => $request['list_order'],
        'use_category' => $request['use_category'],
        'use_latest' => $request['use_latest'],
        'use_comment' => $request['use_comment'],
        'use_mail_post' => $request['use_mail_post'],
        'use_mail_comment' => $request['use_mail_comment'],
        'mail_admin' => $request['mail_admin'],
        'upload_file_count' => $request['upload_file_count'],
        'upload_file_size' => $request['upload_file_size'],
        'upload_extensions' => $request['upload_extensions'],
        'use_point' => $request['use_point'],
        'point_write' => $request['point_write'],
        'point_comment' => $request['point_comment'],
        'point_read' => $request['point_read'],
        'status' => $request['status'],
        'updated_at' => G5_TIME_YMDHIS,
    );

    if (!sql_begin_transaction()) {
        return array('error' => '트랜잭션을 시작하지 못했습니다.', 'board_id' => $request['board_id']);
    }

    if ($is_update) {
        $query_params = $params;
        $query_params['original_board_id'] = $request['original_board_id'];
        $sql = " update {$table}
                    set name = :name,
                        description = :description,
                        read_level = :read_level,
                        write_level = :write_level,
                        comment_level = :comment_level,
                        list_order = :list_order,
                        use_category = :use_category,
                        use_latest = :use_latest,
                        use_comment = :use_comment,
                        use_mail_post = :use_mail_post,
                        use_mail_comment = :use_mail_comment,
                        mail_admin = :mail_admin,
                        upload_file_count = :upload_file_count,
                        upload_file_size = :upload_file_size,
                        upload_extensions = :upload_extensions,
                        use_point = :use_point,
                        point_write = :point_write,
                        point_comment = :point_comment,
                        point_read = :point_read,
                        status = :status,
                        updated_at = :updated_at
                  where board_id = :original_board_id ";
    } else {
        $query_params = $params;
        $query_params['board_id'] = $request['board_id'];
        $query_params['created_at'] = G5_TIME_YMDHIS;
        $sql = " insert into {$table}
                    set board_id = :board_id,
                        name = :name,
                        description = :description,
                        read_level = :read_level,
                        write_level = :write_level,
                        comment_level = :comment_level,
                        list_order = :list_order,
                        use_category = :use_category,
                        use_latest = :use_latest,
                        use_comment = :use_comment,
                        use_mail_post = :use_mail_post,
                        use_mail_comment = :use_mail_comment,
                        mail_admin = :mail_admin,
                        upload_file_count = :upload_file_count,
                        upload_file_size = :upload_file_size,
                        upload_extensions = :upload_extensions,
                        use_point = :use_point,
                        point_write = :point_write,
                        point_comment = :point_comment,
                        point_read = :point_read,
                        status = :status,
                        created_at = :created_at,
                        updated_at = :updated_at ";
    }

    if (!sql_query_prepared($sql, $query_params, false)) {
        sql_rollback();
        return array('error' => '게시판 정보를 저장하지 못했습니다.', 'board_id' => $request['board_id']);
    }

    community_admin_save_board_categories($request['board_id'], $request['categories']);

    if (!sql_commit()) {
        sql_rollback();
        return array('error' => '게시판 저장을 완료하지 못했습니다.', 'board_id' => $request['board_id']);
    }

    return array('error' => '', 'board_id' => $request['board_id']);
}

function community_admin_notification_table()
{
    global $g5;

    return $g5['community_notification_table'];
}

function community_admin_build_notification_search_sql(array $request, array &$params)
{
    $where = array('1=1');

    if ($request['status'] !== '') {
        $where[] = 'status = :status';
        $params['status'] = $request['status'];
    }

    if ($request['stx'] !== '') {
        $where[] = '(recipient_mb_id like :stx_like or recipient_email like :stx_like or subject like :stx_like)';
        $params['stx_like'] = '%' . $request['stx'] . '%';
    }

    return ' where ' . implode(' and ', $where);
}

function community_admin_fetch_notification_log_page(array $request)
{
    $table = community_admin_notification_table();
    $params = array();
    $where = community_admin_build_notification_search_sql($request, $params);
    $count_row = sql_fetch_prepared(" select count(*) as cnt from {$table} {$where} ", $params);
    $total_count = isset($count_row['cnt']) ? (int) $count_row['cnt'] : 0;
    $from_record = ($request['page'] - 1) * $request['page_rows'];

    $list_params = $params;
    $list_params['from_record'] = $from_record;
    $list_params['page_rows'] = $request['page_rows'];

    $rows = sql_fetch_all_prepared(
        " select * from {$table} {$where}
          order by notification_id desc
          limit :from_record, :page_rows ",
        $list_params
    );

    return array(
        'total_count' => $total_count,
        'rows' => $rows,
        'from_record' => $from_record,
    );
}
