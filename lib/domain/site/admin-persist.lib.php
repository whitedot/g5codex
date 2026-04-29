<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function site_admin_fetch_page($page_id)
{
    site_ensure_page_schema();
    $table = site_page_table();

    return sql_fetch_prepared(" select * from {$table} where page_id = :page_id ", array('page_id' => (int) $page_id));
}

function site_admin_fetch_page_by_slug($slug)
{
    site_ensure_page_schema();
    $table = site_page_table();

    return sql_fetch_prepared(" select * from {$table} where slug = :slug ", array('slug' => site_normalize_page_slug($slug)));
}

function site_admin_build_page_search_sql(array $request, array &$params)
{
    $where = array('1=1');
    if ($request['content_format'] !== '') {
        $where[] = 'content_format = :content_format';
        $params['content_format'] = $request['content_format'];
    }
    if ($request['status'] !== '') {
        $where[] = 'status = :status';
        $params['status'] = $request['status'];
    }
    if ($request['stx'] !== '') {
        $where[] = '(title like :stx_like or slug like :stx_like or summary like :stx_like)';
        $params['stx_like'] = '%' . $request['stx'] . '%';
    }

    return ' where ' . implode(' and ', $where);
}

function site_admin_fetch_page_list_page(array $request)
{
    site_ensure_page_schema();
    $table = site_page_table();
    $params = array();
    $where = site_admin_build_page_search_sql($request, $params);
    $count_row = sql_fetch_prepared(" select count(*) as cnt from {$table} {$where} ", $params);
    $total_count = isset($count_row['cnt']) ? (int) $count_row['cnt'] : 0;
    $from_record = ($request['page'] - 1) * $request['page_rows'];

    $list_params = $params;
    $list_params['from_record'] = $from_record;
    $list_params['page_rows'] = $request['page_rows'];
    $rows = sql_fetch_all_prepared(
        " select * from {$table} {$where}
          order by list_order asc, page_id desc
          limit :from_record, :page_rows ",
        $list_params
    );

    return array(
        'total_count' => $total_count,
        'rows' => $rows,
        'from_record' => $from_record,
    );
}

function site_admin_validate_page_request(array $request)
{
    if ($request['slug'] === '') {
        return '페이지 ID를 입력하세요.';
    }

    if ($request['title'] === '') {
        return '페이지 제목을 입력하세요.';
    }

    if ($request['content'] === '') {
        return '페이지 내용을 입력하세요.';
    }

    $existing = site_admin_fetch_page_by_slug($request['slug']);
    if (!empty($existing['page_id']) && (int) $existing['page_id'] !== (int) $request['page_id']) {
        return '이미 사용 중인 페이지 ID입니다.';
    }

    return '';
}

function site_admin_save_page(array $request)
{
    site_ensure_page_schema();
    $error = site_admin_validate_page_request($request);
    if ($error !== '') {
        return array('error' => $error, 'page_id' => $request['page_id']);
    }

    $content = $request['content_format'] === 'html' ? html_purifier($request['content']) : $request['content'];
    $table = site_page_table();
    $params = array(
        'slug' => $request['slug'],
        'title' => $request['title'],
        'summary' => $request['summary'],
        'content' => $content,
        'content_format' => $request['content_format'],
        'access_level' => $request['access_level'],
        'show_pc' => $request['show_pc'],
        'show_mobile' => $request['show_mobile'],
        'list_order' => $request['list_order'],
        'status' => $request['status'],
        'updated_at' => G5_TIME_YMDHIS,
    );

    if ($request['page_id'] > 0) {
        $params['page_id'] = $request['page_id'];
        $sql = " update {$table}
                    set slug = :slug,
                        title = :title,
                        summary = :summary,
                        content = :content,
                        content_format = :content_format,
                        access_level = :access_level,
                        show_pc = :show_pc,
                        show_mobile = :show_mobile,
                        list_order = :list_order,
                        status = :status,
                        updated_at = :updated_at
                  where page_id = :page_id ";
    } else {
        $params['created_at'] = G5_TIME_YMDHIS;
        $sql = " insert into {$table}
                    set slug = :slug,
                        title = :title,
                        summary = :summary,
                        content = :content,
                        content_format = :content_format,
                        access_level = :access_level,
                        show_pc = :show_pc,
                        show_mobile = :show_mobile,
                        list_order = :list_order,
                        status = :status,
                        created_at = :created_at,
                        updated_at = :updated_at ";
    }

    if (!sql_query_prepared($sql, $params, false)) {
        return array('error' => '페이지를 저장하지 못했습니다.', 'page_id' => $request['page_id']);
    }

    return array('error' => '', 'page_id' => $request['page_id'] > 0 ? $request['page_id'] : sql_insert_id());
}
