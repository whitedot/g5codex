<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_scrap_table()
{
    global $g5;

    return $g5['community_scrap_table'];
}

function community_has_scrap($mb_id, $post_id)
{
    if ($mb_id === '' || (int) $post_id < 1) {
        return false;
    }

    $table = community_scrap_table();
    $row = sql_fetch_prepared(
        " select scrap_id from {$table}
          where mb_id = :mb_id and post_id = :post_id
          limit 1 ",
        array(
            'mb_id' => $mb_id,
            'post_id' => (int) $post_id,
        )
    );

    return !empty($row['scrap_id']);
}

function community_add_scrap($mb_id, array $post)
{
    if ($mb_id === '' || empty($post['post_id'])) {
        return false;
    }

    $table = community_scrap_table();

    return (bool) sql_query_prepared(
        " insert into {$table}
            set mb_id = :mb_id,
                board_id = :board_id,
                post_id = :post_id,
                created_at = :created_at
          on duplicate key update
                board_id = values(board_id) ",
        array(
            'mb_id' => $mb_id,
            'board_id' => $post['board_id'],
            'post_id' => (int) $post['post_id'],
            'created_at' => G5_TIME_YMDHIS,
        ),
        false
    );
}

function community_delete_scrap($mb_id, $post_id)
{
    if ($mb_id === '' || (int) $post_id < 1) {
        return false;
    }

    $table = community_scrap_table();

    return (bool) sql_query_prepared(
        " delete from {$table}
          where mb_id = :mb_id and post_id = :post_id ",
        array(
            'mb_id' => $mb_id,
            'post_id' => (int) $post_id,
        ),
        false
    );
}

function community_toggle_scrap($mb_id, array $post)
{
    if (community_has_scrap($mb_id, $post['post_id'])) {
        community_delete_scrap($mb_id, $post['post_id']);
        return array('error' => '', 'scrapped' => false);
    }

    if (!community_add_scrap($mb_id, $post)) {
        return array('error' => '스크랩을 저장하지 못했습니다.', 'scrapped' => false);
    }

    return array('error' => '', 'scrapped' => true);
}

function community_fetch_scrap_page($mb_id, array $request, $member_level = 1)
{
    $scrap_table = community_scrap_table();
    $post_table = community_post_table();
    $board_table = community_board_table();
    $params = array(
        'mb_id' => $mb_id,
        'member_level' => (int) $member_level,
    );

    $count_row = sql_fetch_prepared(
        " select count(*) as cnt
            from {$scrap_table} s
            join {$post_table} p on p.post_id = s.post_id and p.status = 'published'
            join {$board_table} b on b.board_id = p.board_id and b.status = 'active'
           where s.mb_id = :mb_id
             and b.read_level <= :member_level ",
        $params
    );
    $total_count = isset($count_row['cnt']) ? (int) $count_row['cnt'] : 0;
    $from_record = ($request['page'] - 1) * $request['page_rows'];

    $params['from_record'] = $from_record;
    $params['page_rows'] = $request['page_rows'];
    $rows = sql_fetch_all_prepared(
        " select s.scrap_id, s.created_at as scrapped_at,
                 p.*, b.name as board_name, b.read_level
            from {$scrap_table} s
            join {$post_table} p on p.post_id = s.post_id and p.status = 'published'
            join {$board_table} b on b.board_id = p.board_id and b.status = 'active'
           where s.mb_id = :mb_id
             and b.read_level <= :member_level
           order by s.created_at desc, s.scrap_id desc
           limit :from_record, :page_rows ",
        $params
    );

    return array(
        'total_count' => $total_count,
        'rows' => $rows,
        'from_record' => $from_record,
    );
}
