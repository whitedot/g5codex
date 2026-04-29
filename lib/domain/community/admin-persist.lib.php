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
    community_ensure_operation_schema();
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
    community_ensure_operation_schema();
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
        'group_id' => $request['group_id'],
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
                        group_id = :group_id,
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
                        group_id = :group_id,
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

    community_cache_delete_group('community:board:' . $request['board_id'] . ':');
    community_cache_delete('community:board:list:active');
    community_cache_delete_group('community:board:list:active:');
    community_admin_save_board_categories($request['board_id'], $request['categories']);
    if (!community_rebuild_latest_board(array(
        'board_id' => $request['board_id'],
        'use_latest' => $request['use_latest'],
        'status' => $request['status'],
    ))) {
        sql_rollback();
        return array('error' => '최신글 인덱스를 갱신하지 못했습니다.', 'board_id' => $request['board_id']);
    }

    if (!sql_commit()) {
        sql_rollback();
        return array('error' => '게시판 저장을 완료하지 못했습니다.', 'board_id' => $request['board_id']);
    }

    return array('error' => '', 'board_id' => $request['board_id']);
}

function community_admin_save_config(array $request)
{
    if (!community_set_config_values($request)) {
        return array('error' => '커뮤니티 기본환경 설정 테이블을 준비하지 못했습니다.');
    }

    return array('error' => '');
}

function community_admin_group_table()
{
    return community_board_group_table();
}

function community_admin_fetch_group($group_id)
{
    community_ensure_operation_schema();
    $table = community_admin_group_table();

    return sql_fetch_prepared(" select * from {$table} where group_id = :group_id ", array('group_id' => $group_id));
}

function community_admin_build_group_search_sql(array $request, array &$params)
{
    $where = array('1=1');

    if ($request['status'] !== '') {
        $where[] = 'status = :status';
        $params['status'] = $request['status'];
    }

    if ($request['stx'] !== '') {
        $where[] = '(group_id like :stx_like or name like :stx_like)';
        $params['stx_like'] = '%' . $request['stx'] . '%';
    }

    return ' where ' . implode(' and ', $where);
}

function community_admin_fetch_group_list_page(array $request)
{
    community_ensure_operation_schema();
    $table = community_admin_group_table();
    $board_table = community_admin_board_table();
    $params = array();
    $where = community_admin_build_group_search_sql($request, $params);
    $count_row = sql_fetch_prepared(" select count(*) as cnt from {$table} {$where} ", $params);
    $total_count = isset($count_row['cnt']) ? (int) $count_row['cnt'] : 0;
    $from_record = ($request['page'] - 1) * $request['page_rows'];

    $list_params = $params;
    $list_params['from_record'] = $from_record;
    $list_params['page_rows'] = $request['page_rows'];

    $rows = sql_fetch_all_prepared(
        " select g.*,
                 (select count(*) from {$board_table} b where b.group_id = g.group_id) as board_count
            from {$table} g
            {$where}
           order by g.list_order asc, g.group_id asc
           limit :from_record, :page_rows ",
        $list_params
    );

    return array(
        'total_count' => $total_count,
        'rows' => $rows,
        'from_record' => $from_record,
    );
}

function community_admin_validate_group_request(array $request, $is_update)
{
    if ($request['group_id'] === '') {
        return '그룹 ID를 입력하세요.';
    }

    if (!preg_match('/^[a-z][a-z0-9_]{1,49}$/i', $request['group_id'])) {
        return '그룹 ID는 영문자로 시작하고 영문자, 숫자, _ 만 사용할 수 있습니다.';
    }

    if ($request['name'] === '') {
        return '그룹 이름을 입력하세요.';
    }

    if (!in_array($request['status'], community_admin_board_status_values(), true)) {
        return '그룹 상태가 올바르지 않습니다.';
    }

    if ($is_update && $request['original_group_id'] !== $request['group_id']) {
        return '그룹 ID는 수정할 수 없습니다.';
    }

    $existing = community_admin_fetch_group($request['group_id']);
    if (!$is_update && isset($existing['group_id']) && $existing['group_id'] !== '') {
        return '이미 존재하는 그룹 ID입니다.';
    }

    return '';
}

function community_admin_save_group(array $request)
{
    community_ensure_operation_schema();
    $is_update = ($request['original_group_id'] !== '');
    $error = community_admin_validate_group_request($request, $is_update);

    if ($error !== '') {
        return array('error' => $error, 'group_id' => $request['group_id']);
    }

    $table = community_admin_group_table();
    $params = array(
        'name' => $request['name'],
        'description' => $request['description'],
        'read_level' => $request['read_level'],
        'write_level' => $request['write_level'],
        'comment_level' => $request['comment_level'],
        'list_order' => $request['list_order'],
        'status' => $request['status'],
        'updated_at' => G5_TIME_YMDHIS,
    );

    if ($is_update) {
        $params['original_group_id'] = $request['original_group_id'];
        $sql = " update {$table}
                    set name = :name,
                        description = :description,
                        read_level = :read_level,
                        write_level = :write_level,
                        comment_level = :comment_level,
                        list_order = :list_order,
                        status = :status,
                        updated_at = :updated_at
                  where group_id = :original_group_id ";
    } else {
        $params['group_id'] = $request['group_id'];
        $params['created_at'] = G5_TIME_YMDHIS;
        $sql = " insert into {$table}
                    set group_id = :group_id,
                        name = :name,
                        description = :description,
                        read_level = :read_level,
                        write_level = :write_level,
                        comment_level = :comment_level,
                        list_order = :list_order,
                        status = :status,
                        created_at = :created_at,
                        updated_at = :updated_at ";
    }

    if (!sql_query_prepared($sql, $params, false)) {
        return array('error' => '게시판 그룹을 저장하지 못했습니다.', 'group_id' => $request['group_id']);
    }

    return array('error' => '', 'group_id' => $request['group_id']);
}

function community_admin_fetch_group_options()
{
    community_ensure_operation_schema();
    $table = community_admin_group_table();

    return sql_fetch_all_prepared(
        " select group_id, name from {$table} where status <> 'archived' order by list_order asc, group_id asc ",
        array()
    );
}

function community_admin_menu_table()
{
    return site_menu_table();
}

function community_admin_fetch_menu($menu_id)
{
    community_ensure_operation_schema();
    $table = community_admin_menu_table();

    return sql_fetch_prepared(" select * from {$table} where menu_id = :menu_id ", array('menu_id' => (int) $menu_id));
}

function community_admin_build_menu_search_sql(array $request, array &$params)
{
    $where = array('1=1');
    if ($request['status'] !== '') {
        $where[] = 'm.status = :status';
        $params['status'] = $request['status'];
    }
    if ($request['stx'] !== '') {
        $where[] = '(m.name like :stx_like or m.target_id like :stx_like or m.url like :stx_like)';
        $params['stx_like'] = '%' . $request['stx'] . '%';
    }

    return ' where ' . implode(' and ', $where);
}

function community_admin_fetch_menu_list_page(array $request)
{
    community_ensure_operation_schema();
    $table = community_admin_menu_table();
    $params = array();
    $where = community_admin_build_menu_search_sql($request, $params);
    $count_row = sql_fetch_prepared(" select count(*) as cnt from {$table} {$where} ", $params);
    $total_count = isset($count_row['cnt']) ? (int) $count_row['cnt'] : 0;
    $from_record = ($request['page'] - 1) * $request['page_rows'];

    $list_params = $params;
    $list_params['from_record'] = $from_record;
    $list_params['page_rows'] = $request['page_rows'];
    $rows = sql_fetch_all_prepared(
        " select m.*, p.name as parent_name
            from {$table} m
            left join {$table} p on p.menu_id = m.parent_id
            {$where}
           order by m.parent_id asc, m.list_order asc, m.menu_id asc
           limit :from_record, :page_rows ",
        $list_params
    );

    return array(
        'total_count' => $total_count,
        'rows' => $rows,
        'from_record' => $from_record,
    );
}

function community_admin_validate_menu_request(array $request)
{
    if ($request['name'] === '') {
        return '메뉴명을 입력하세요.';
    }

    if ($request['parent_id'] > 0 && $request['parent_id'] === $request['menu_id']) {
        return '상위 메뉴를 자기 자신으로 지정할 수 없습니다.';
    }

    if ($request['menu_type'] === 'url' && $request['url'] === '') {
        return '직접 URL 메뉴는 URL을 입력하세요.';
    }

    if (in_array($request['menu_type'], array('page', 'board_group', 'board'), true) && $request['target_id'] === '') {
        return '연결 대상을 입력하세요.';
    }

    return '';
}

function community_admin_save_menu(array $request)
{
    community_ensure_operation_schema();
    $error = community_admin_validate_menu_request($request);
    if ($error !== '') {
        return array('error' => $error, 'menu_id' => $request['menu_id']);
    }

    $table = community_admin_menu_table();
    $params = array(
        'parent_id' => $request['parent_id'],
        'menu_type' => $request['menu_type'],
        'target_id' => $request['target_id'],
        'name' => $request['name'],
        'url' => $request['url'],
        'target_blank' => $request['target_blank'],
        'access_level' => $request['access_level'],
        'show_pc' => $request['show_pc'],
        'show_mobile' => $request['show_mobile'],
        'list_order' => $request['list_order'],
        'status' => $request['status'],
        'updated_at' => G5_TIME_YMDHIS,
    );

    if ($request['menu_id'] > 0) {
        $params['menu_id'] = $request['menu_id'];
        $sql = " update {$table}
                    set parent_id = :parent_id,
                        menu_type = :menu_type,
                        target_id = :target_id,
                        name = :name,
                        url = :url,
                        target_blank = :target_blank,
                        access_level = :access_level,
                        show_pc = :show_pc,
                        show_mobile = :show_mobile,
                        list_order = :list_order,
                        status = :status,
                        updated_at = :updated_at
                  where menu_id = :menu_id ";
    } else {
        $params['created_at'] = G5_TIME_YMDHIS;
        $sql = " insert into {$table}
                    set parent_id = :parent_id,
                        menu_type = :menu_type,
                        target_id = :target_id,
                        name = :name,
                        url = :url,
                        target_blank = :target_blank,
                        access_level = :access_level,
                        show_pc = :show_pc,
                        show_mobile = :show_mobile,
                        list_order = :list_order,
                        status = :status,
                        created_at = :created_at,
                        updated_at = :updated_at ";
    }

    if (!sql_query_prepared($sql, $params, false)) {
        return array('error' => '메뉴를 저장하지 못했습니다.', 'menu_id' => $request['menu_id']);
    }

    return array('error' => '', 'menu_id' => $request['menu_id'] > 0 ? $request['menu_id'] : sql_insert_id());
}

function community_admin_fetch_parent_menu_options($exclude_menu_id = 0)
{
    community_ensure_operation_schema();
    $table = community_admin_menu_table();

    return sql_fetch_all_prepared(
        " select menu_id, name from {$table}
          where parent_id = 0 and menu_id <> :exclude_menu_id
          order by list_order asc, menu_id asc ",
        array('exclude_menu_id' => (int) $exclude_menu_id)
    );
}

function community_admin_banner_table()
{
    return site_banner_table();
}

function community_admin_fetch_banner($banner_id)
{
    community_ensure_operation_schema();
    $table = community_admin_banner_table();

    return sql_fetch_prepared(" select * from {$table} where banner_id = :banner_id ", array('banner_id' => (int) $banner_id));
}

function community_admin_build_banner_search_sql(array $request, array &$params)
{
    $where = array('1=1');
    if ($request['position'] !== '') {
        $where[] = 'position = :position';
        $params['position'] = $request['position'];
    }
    if ($request['status'] !== '') {
        $where[] = 'status = :status';
        $params['status'] = $request['status'];
    }
    if ($request['stx'] !== '') {
        $where[] = '(title like :stx_like or link_url like :stx_like)';
        $params['stx_like'] = '%' . $request['stx'] . '%';
    }

    return ' where ' . implode(' and ', $where);
}

function community_admin_fetch_banner_list_page(array $request)
{
    community_ensure_operation_schema();
    $table = community_admin_banner_table();
    $params = array();
    $where = community_admin_build_banner_search_sql($request, $params);
    $count_row = sql_fetch_prepared(" select count(*) as cnt from {$table} {$where} ", $params);
    $total_count = isset($count_row['cnt']) ? (int) $count_row['cnt'] : 0;
    $from_record = ($request['page'] - 1) * $request['page_rows'];

    $list_params = $params;
    $list_params['from_record'] = $from_record;
    $list_params['page_rows'] = $request['page_rows'];
    $rows = sql_fetch_all_prepared(
        " select * from {$table} {$where}
          order by position asc, list_order asc, banner_id desc
          limit :from_record, :page_rows ",
        $list_params
    );

    return array(
        'total_count' => $total_count,
        'rows' => $rows,
        'from_record' => $from_record,
    );
}

function community_admin_banner_upload_dir()
{
    return 'banner/' . date('Ym', G5_SERVER_TIME);
}

function community_admin_store_banner_upload(array $file)
{
    if (empty($file['name']) || (int) $file['error'] === UPLOAD_ERR_NO_FILE) {
        return array('error' => '', 'path' => '');
    }

    if ((int) $file['error'] !== UPLOAD_ERR_OK) {
        return array('error' => '배너 이미지를 업로드하지 못했습니다.', 'path' => '');
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, array('jpg', 'jpeg', 'png', 'gif', 'webp'), true)) {
        return array('error' => '배너 이미지는 jpg, png, gif, webp만 업로드할 수 있습니다.', 'path' => '');
    }

    $relative_dir = community_admin_banner_upload_dir();
    $absolute_dir = G5_DATA_PATH . '/' . $relative_dir;
    if (!is_dir($absolute_dir) && !mkdir($absolute_dir, G5_DIR_PERMISSION, true)) {
        return array('error' => '배너 이미지 저장 디렉터리를 만들지 못했습니다.', 'path' => '');
    }

    $filename = 'banner-' . date('YmdHis', G5_SERVER_TIME) . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
    $relative_path = $relative_dir . '/' . $filename;
    $absolute_path = G5_DATA_PATH . '/' . $relative_path;

    if (!is_uploaded_file($file['tmp_name']) || !move_uploaded_file($file['tmp_name'], $absolute_path)) {
        return array('error' => '배너 이미지 파일을 저장하지 못했습니다.', 'path' => '');
    }

    @chmod($absolute_path, G5_FILE_PERMISSION);

    return array('error' => '', 'path' => $relative_path);
}

function community_admin_validate_banner_request(array $request)
{
    if ($request['title'] === '') {
        return '배너명을 입력하세요.';
    }

    if ($request['ended_at'] !== '0000-00-00 00:00:00' && $request['started_at'] !== '0000-00-00 00:00:00' && $request['ended_at'] < $request['started_at']) {
        return '종료 일시는 시작 일시보다 빠를 수 없습니다.';
    }

    return '';
}

function community_admin_save_banner(array $request, array $files)
{
    community_ensure_operation_schema();
    $error = community_admin_validate_banner_request($request);
    if ($error !== '') {
        return array('error' => $error, 'banner_id' => $request['banner_id']);
    }

    $current = $request['banner_id'] > 0 ? community_admin_fetch_banner($request['banner_id']) : array();
    $image_path = !empty($current['image_path']) ? $current['image_path'] : $request['image_path'];
    $mobile_image_path = !empty($current['mobile_image_path']) ? $current['mobile_image_path'] : $request['mobile_image_path'];

    if ($request['delete_image']) {
        $image_path = '';
    }
    if ($request['delete_mobile_image']) {
        $mobile_image_path = '';
    }

    if (isset($files['image_file'])) {
        $upload = community_admin_store_banner_upload($files['image_file']);
        if ($upload['error'] !== '') {
            return array('error' => $upload['error'], 'banner_id' => $request['banner_id']);
        }
        if ($upload['path'] !== '') {
            $image_path = $upload['path'];
        }
    }

    if (isset($files['mobile_image_file'])) {
        $upload = community_admin_store_banner_upload($files['mobile_image_file']);
        if ($upload['error'] !== '') {
            return array('error' => $upload['error'], 'banner_id' => $request['banner_id']);
        }
        if ($upload['path'] !== '') {
            $mobile_image_path = $upload['path'];
        }
    }

    $table = community_admin_banner_table();
    $params = array(
        'position' => $request['position'],
        'title' => $request['title'],
        'image_path' => $image_path,
        'mobile_image_path' => $mobile_image_path,
        'link_url' => $request['link_url'],
        'target_blank' => $request['target_blank'],
        'started_at' => $request['started_at'],
        'ended_at' => $request['ended_at'],
        'show_pc' => $request['show_pc'],
        'show_mobile' => $request['show_mobile'],
        'list_order' => $request['list_order'],
        'status' => $request['status'],
        'updated_at' => G5_TIME_YMDHIS,
    );

    if ($request['banner_id'] > 0) {
        $params['banner_id'] = $request['banner_id'];
        $sql = " update {$table}
                    set position = :position,
                        title = :title,
                        image_path = :image_path,
                        mobile_image_path = :mobile_image_path,
                        link_url = :link_url,
                        target_blank = :target_blank,
                        started_at = :started_at,
                        ended_at = :ended_at,
                        show_pc = :show_pc,
                        show_mobile = :show_mobile,
                        list_order = :list_order,
                        status = :status,
                        updated_at = :updated_at
                  where banner_id = :banner_id ";
    } else {
        $params['created_at'] = G5_TIME_YMDHIS;
        $sql = " insert into {$table}
                    set position = :position,
                        title = :title,
                        image_path = :image_path,
                        mobile_image_path = :mobile_image_path,
                        link_url = :link_url,
                        target_blank = :target_blank,
                        started_at = :started_at,
                        ended_at = :ended_at,
                        show_pc = :show_pc,
                        show_mobile = :show_mobile,
                        list_order = :list_order,
                        status = :status,
                        created_at = :created_at,
                        updated_at = :updated_at ";
    }

    if (!sql_query_prepared($sql, $params, false)) {
        return array('error' => '배너를 저장하지 못했습니다.', 'banner_id' => $request['banner_id']);
    }

    return array('error' => '', 'banner_id' => $request['banner_id'] > 0 ? $request['banner_id'] : sql_insert_id());
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
          order by created_at desc, post_id desc
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
        $where[] = 'c.post_id = :post_id';
        $params['post_id'] = $request['post_id'];
    }

    if ($request['status'] !== '') {
        $where[] = 'c.status = :status';
        $params['status'] = $request['status'];
    }

    if ($request['stx'] !== '') {
        $where[] = '(c.content like :stx_like or c.mb_id like :stx_like or p.title like :stx_like)';
        $params['stx_like'] = '%' . $request['stx'] . '%';
    }

    return ' where ' . implode(' and ', $where);
}

function community_admin_fetch_comment_list_page(array $request)
{
    $table = community_admin_comment_table();
    $post_table = community_admin_post_table();
    $params = array();
    $where = community_admin_build_comment_search_sql($request, $params);
    $count_row = sql_fetch_prepared(
        " select count(*) as cnt
            from {$table} c
            left join {$post_table} p on p.post_id = c.post_id
            {$where} ",
        $params
    );
    $total_count = isset($count_row['cnt']) ? (int) $count_row['cnt'] : 0;
    $from_record = ($request['page'] - 1) * $request['page_rows'];

    $list_params = $params;
    $list_params['from_record'] = $from_record;
    $list_params['page_rows'] = $request['page_rows'];

    $rows = sql_fetch_all_prepared(
        " select c.*, p.board_id as post_board_id, p.title as post_title
            from {$table} c
            left join {$post_table} p on p.post_id = c.post_id
            {$where}
          order by c.comment_id desc
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
                community_recalculate_post_comment_summary($comment['post_id']);
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

function community_admin_point_wallet_table()
{
    global $g5;

    return $g5['community_point_wallet_table'];
}

function community_admin_point_ledger_table()
{
    global $g5;

    return $g5['community_point_ledger_table'];
}

function community_admin_health_table_checks()
{
    global $g5;

    $tables = array(
        '기본환경 설정' => $g5['community_config_table'],
        '게시판 그룹' => $g5['community_board_group_table'],
        '게시판' => $g5['community_board_table'],
        '카테고리' => $g5['community_board_category_table'],
        '게시글' => $g5['community_post_table'],
        '댓글' => $g5['community_comment_table'],
        '최신글' => $g5['community_latest_table'],
        '포인트 지갑' => $g5['community_point_wallet_table'],
        '포인트 원장' => $g5['community_point_ledger_table'],
        '사용 가능 포인트' => $g5['community_point_available_table'],
        '첨부파일' => $g5['community_attachment_table'],
        '스크랩' => $g5['community_scrap_table'],
        '사이트 메뉴' => $g5['site_menu_table'],
        '사이트 배너' => $g5['site_banner_table'],
    );

    $checks = array();
    foreach ($tables as $label => $table) {
        $exists = sql_table_exists($table);
        $checks[] = array(
            'label' => '테이블: ' . $label,
            'status' => $exists ? 'ok' : 'error',
            'message' => $exists ? $table . ' 확인됨' : $table . ' 없음',
            'action' => $exists ? '' : '신규 설치 또는 install/community_schema.sql 적용 필요',
        );
    }

    return $checks;
}

function community_admin_health_runtime_checks()
{
    global $config;

    $checks = array();
    $checks[] = array(
        'label' => '커뮤니티 경로',
        'status' => is_dir(G5_COMMUNITY_PATH) && is_dir(G5_COMMUNITY_VIEW_PATH) ? 'ok' : 'error',
        'message' => G5_COMMUNITY_PATH,
        'action' => is_dir(G5_COMMUNITY_PATH) ? '' : 'community 디렉터리 배포 확인',
    );

    $attachment_base = community_attachment_base_path();
    $attachment_ready = (is_dir($attachment_base) && is_writable($attachment_base)) || (!is_dir($attachment_base) && is_writable(G5_DATA_PATH));
    $checks[] = array(
        'label' => '첨부파일 저장소',
        'status' => $attachment_ready ? 'ok' : 'warning',
        'message' => $attachment_base,
        'action' => $attachment_ready ? '' : 'data 디렉터리 권한 확인',
    );

    $mail_enabled = !empty($config['cf_email_use']);
    $checks[] = array(
        'label' => '메일 발송 설정',
        'status' => $mail_enabled ? 'ok' : 'warning',
        'message' => $mail_enabled ? '메일 발송 사용' : '메일 발송 미사용',
        'action' => $mail_enabled ? '' : '알림 메일을 쓰려면 환경설정에서 메일 발송 활성화',
    );

    return $checks;
}

function community_admin_health_content_checks()
{
    global $g5;

    $checks = array();
    if (!sql_table_exists($g5['community_board_table'])) {
        return $checks;
    }

    $row = sql_fetch_prepared(
        " select count(*) as cnt from {$g5['community_board_table']} where status = 'active' ",
        array()
    );
    $active_count = isset($row['cnt']) ? (int) $row['cnt'] : 0;
    $checks[] = array(
        'label' => '활성 게시판',
        'status' => $active_count > 0 ? 'ok' : 'warning',
        'message' => number_format($active_count) . '개',
        'action' => $active_count > 0 ? '' : '게시판 관리에서 활성 게시판 생성',
    );

    return $checks;
}

function community_admin_fetch_health_checks()
{
    return array_merge(
        community_admin_health_table_checks(),
        community_admin_health_runtime_checks(),
        community_admin_health_content_checks()
    );
}

function community_admin_fetch_point_wallet_page(array $request)
{
    $table = community_admin_point_wallet_table();
    $where = ' where 1=1 ';
    $params = array();

    if ($request['mb_id'] !== '') {
        $where .= ' and mb_id = :mb_id ';
        $params['mb_id'] = $request['mb_id'];
    }

    $count_row = sql_fetch_prepared(" select count(*) as cnt from {$table} {$where} ", $params);
    $total_count = isset($count_row['cnt']) ? (int) $count_row['cnt'] : 0;
    $from_record = ($request['page'] - 1) * $request['page_rows'];

    $list_params = $params;
    $list_params['from_record'] = $from_record;
    $list_params['page_rows'] = $request['page_rows'];

    $rows = sql_fetch_all_prepared(
        " select * from {$table} {$where}
          order by balance desc, mb_id asc
          limit :from_record, :page_rows ",
        $list_params
    );

    foreach ($rows as $index => $row) {
        $recalculated = community_point_recalculate_wallet($row['mb_id']);
        if (!empty($recalculated['mb_id'])) {
            $rows[$index] = $recalculated;
        }
    }

    return array(
        'total_count' => $total_count,
        'rows' => $rows,
        'from_record' => $from_record,
    );
}

function community_admin_fetch_point_ledger_rows($mb_id, $limit = 20)
{
    $table = community_admin_point_ledger_table();
    $params = array('page_rows' => max(1, min(50, (int) $limit)));
    $where = ' where 1=1 ';

    if ($mb_id !== '') {
        $where .= ' and mb_id = :mb_id ';
        $params['mb_id'] = $mb_id;
    }

    return sql_fetch_all_prepared(
        " select * from {$table} {$where}
          order by ledger_id desc
          limit :page_rows ",
        $params
    );
}

function community_admin_adjust_point(array $request, array $member)
{
    if ($request['mb_id'] === '') {
        return array('error' => '회원 ID를 입력하세요.');
    }

    if ($request['amount'] === 0) {
        return array('error' => '조정 포인트를 입력하세요.');
    }

    $reason = $request['memo'] !== '' ? $request['memo'] : 'admin_adjust';
    $result = community_point_adjust($request['mb_id'], $request['amount'], array(
        'reason' => $reason,
        'target_type' => 'admin',
        'target_id' => 0,
        'created_by' => isset($member['mb_id']) ? $member['mb_id'] : '',
    ));

    return array('error' => $result['error']);
}

function community_admin_expire_points(array $request)
{
    $result = community_point_expire_available('', '', 1000);

    return array(
        'error' => '',
        'expired_count' => (int) $result['expired_count'],
        'expired_amount' => (int) $result['expired_amount'],
        'has_more' => !empty($result['has_more']),
    );
}
