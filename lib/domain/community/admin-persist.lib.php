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

function community_admin_post_table()
{
    global $g5;

    return $g5['community_post_table'];
}

function community_admin_comment_table()
{
    global $g5;

    return $g5['community_comment_table'];
}

function community_admin_build_post_search_sql(array $request, array &$params)
{
    $where = array('1=1');

    if ($request['board_id'] !== '') {
        $where[] = 'board_id = :board_id';
        $params['board_id'] = $request['board_id'];
    }

    if ($request['status'] !== '') {
        $where[] = 'status = :status';
        $params['status'] = $request['status'];
    }

    if ($request['stx'] !== '') {
        $where[] = '(title like :stx_like or mb_id like :stx_like)';
        $params['stx_like'] = '%' . $request['stx'] . '%';
    }

    return ' where ' . implode(' and ', $where);
}

function community_admin_fetch_post_list_page(array $request)
{
    $table = community_admin_post_table();
    $params = array();
    $where = community_admin_build_post_search_sql($request, $params);
    $count_row = sql_fetch_prepared(" select count(*) as cnt from {$table} {$where} ", $params);
    $total_count = isset($count_row['cnt']) ? (int) $count_row['cnt'] : 0;
    $from_record = ($request['page'] - 1) * $request['page_rows'];

    $list_params = $params;
    $list_params['from_record'] = $from_record;
    $list_params['page_rows'] = $request['page_rows'];

    $rows = sql_fetch_all_prepared(
        " select * from {$table} {$where}
          order by post_id desc
          limit :from_record, :page_rows ",
        $list_params
    );

    return array(
        'total_count' => $total_count,
        'rows' => $rows,
        'from_record' => $from_record,
    );
}

function community_admin_update_post_status($post_id, $status)
{
    $table = community_admin_post_table();
    $deleted_at = $status === 'deleted' ? G5_TIME_YMDHIS : '0000-00-00 00:00:00';

    return (bool) sql_query_prepared(
        " update {$table}
             set status = :status,
                 updated_at = :updated_at,
                 deleted_at = :deleted_at
           where post_id = :post_id ",
        array(
            'post_id' => (int) $post_id,
            'status' => $status,
            'updated_at' => G5_TIME_YMDHIS,
            'deleted_at' => $deleted_at,
        ),
        false
    );
}

function community_admin_update_post_notice($post_id, $is_notice)
{
    $table = community_admin_post_table();

    return (bool) sql_query_prepared(
        " update {$table}
             set is_notice = :is_notice,
                 notice_started_at = :notice_started_at,
                 updated_at = :updated_at
           where post_id = :post_id and status <> 'deleted' ",
        array(
            'post_id' => (int) $post_id,
            'is_notice' => $is_notice ? 1 : 0,
            'notice_started_at' => $is_notice ? G5_TIME_YMDHIS : '0000-00-00 00:00:00',
            'updated_at' => G5_TIME_YMDHIS,
        ),
        false
    );
}

function community_admin_apply_post_action(array $request)
{
    if ($request['action'] === '' || empty($request['post_ids'])) {
        return array('error' => '처리할 게시글과 작업을 선택하세요.', 'count' => 0);
    }

    $count = 0;
    foreach ($request['post_ids'] as $post_id) {
        $post = community_fetch_post($post_id, true);
        if (empty($post['post_id'])) {
            continue;
        }

        $board = community_fetch_board($post['board_id'], true);

        if ($request['action'] === 'delete') {
            $delete_result = community_delete_post_attachments($post['post_id']);
            if ($delete_result['error'] !== '') {
                return array('error' => $delete_result['error'], 'count' => $count);
            }
            community_admin_update_post_status($post['post_id'], 'deleted');
            community_delete_latest_post($post['board_id'], $post['post_id']);
        } elseif ($request['action'] === 'hide') {
            community_admin_update_post_status($post['post_id'], 'hidden');
            community_delete_latest_post($post['board_id'], $post['post_id']);
        } elseif ($request['action'] === 'publish') {
            community_admin_update_post_status($post['post_id'], 'published');
            $post = community_fetch_post($post['post_id'], true);
            community_upsert_latest_post($board, $post);
        } elseif ($request['action'] === 'notice_on') {
            community_admin_update_post_notice($post['post_id'], 1);
            $post = community_fetch_post($post['post_id'], true);
            community_upsert_latest_post($board, $post);
        } elseif ($request['action'] === 'notice_off') {
            community_admin_update_post_notice($post['post_id'], 0);
            $post = community_fetch_post($post['post_id'], true);
            community_upsert_latest_post($board, $post);
        }

        $count++;
    }

    return array('error' => '', 'count' => $count);
}

function community_admin_build_comment_search_sql(array $request, array &$params)
{
    $where = array('1=1');

    if ($request['post_id'] > 0) {
        $where[] = 'post_id = :post_id';
        $params['post_id'] = $request['post_id'];
    }

    if ($request['status'] !== '') {
        $where[] = 'status = :status';
        $params['status'] = $request['status'];
    }

    if ($request['stx'] !== '') {
        $where[] = '(content like :stx_like or mb_id like :stx_like)';
        $params['stx_like'] = '%' . $request['stx'] . '%';
    }

    return ' where ' . implode(' and ', $where);
}

function community_admin_fetch_comment_list_page(array $request)
{
    $table = community_admin_comment_table();
    $params = array();
    $where = community_admin_build_comment_search_sql($request, $params);
    $count_row = sql_fetch_prepared(" select count(*) as cnt from {$table} {$where} ", $params);
    $total_count = isset($count_row['cnt']) ? (int) $count_row['cnt'] : 0;
    $from_record = ($request['page'] - 1) * $request['page_rows'];

    $list_params = $params;
    $list_params['from_record'] = $from_record;
    $list_params['page_rows'] = $request['page_rows'];

    $rows = sql_fetch_all_prepared(
        " select * from {$table} {$where}
          order by comment_id desc
          limit :from_record, :page_rows ",
        $list_params
    );

    return array(
        'total_count' => $total_count,
        'rows' => $rows,
        'from_record' => $from_record,
    );
}

function community_admin_update_comment_status($comment_id, $status)
{
    $table = community_admin_comment_table();
    $deleted_at = $status === 'deleted' ? G5_TIME_YMDHIS : '0000-00-00 00:00:00';

    return (bool) sql_query_prepared(
        " update {$table}
             set status = :status,
                 updated_at = :updated_at,
                 deleted_at = :deleted_at
           where comment_id = :comment_id ",
        array(
            'comment_id' => (int) $comment_id,
            'status' => $status,
            'updated_at' => G5_TIME_YMDHIS,
            'deleted_at' => $deleted_at,
        ),
        false
    );
}

function community_admin_apply_comment_action(array $request)
{
    if ($request['action'] === '' || empty($request['comment_ids'])) {
        return array('error' => '처리할 댓글과 작업을 선택하세요.', 'count' => 0);
    }

    $count = 0;
    foreach ($request['comment_ids'] as $comment_id) {
        $comment = community_fetch_comment($comment_id);
        if (empty($comment['comment_id']) && $request['action'] !== 'publish') {
            continue;
        }

        if ($request['action'] === 'publish') {
            $table = community_admin_comment_table();
            $comment = sql_fetch_prepared(" select * from {$table} where comment_id = :comment_id ", array('comment_id' => (int) $comment_id));
            if (empty($comment['comment_id'])) {
                continue;
            }
            if ($comment['status'] !== 'published') {
                community_admin_update_comment_status($comment['comment_id'], 'published');
                community_increment_post_comment_count($comment['post_id']);
            }
        } elseif ($request['action'] === 'hide') {
            if ($comment['status'] === 'published') {
                community_admin_update_comment_status($comment['comment_id'], 'hidden');
                community_decrement_post_comment_count($comment['post_id']);
            }
        } elseif ($request['action'] === 'delete') {
            if ($comment['status'] === 'published') {
                community_admin_update_comment_status($comment['comment_id'], 'deleted');
                community_decrement_post_comment_count($comment['post_id']);
            } else {
                community_admin_update_comment_status($comment['comment_id'], 'deleted');
            }
        }

        $post = community_fetch_post($comment['post_id'], true);
        $board = !empty($post['board_id']) ? community_fetch_board($post['board_id'], true) : array();
        if (!empty($board['board_id']) && !empty($post['post_id'])) {
            $post = community_fetch_post($post['post_id'], true);
            community_upsert_latest_post($board, $post);
        }

        $count++;
    }

    return array('error' => '', 'count' => $count);
}
