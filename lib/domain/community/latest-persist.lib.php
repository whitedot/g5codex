<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_latest_table()
{
    global $g5;

    return $g5['community_latest_table'];
}

function community_upsert_latest_post(array $board, array $post)
{
    if (empty($board['use_latest']) || empty($post['post_id']) || $post['status'] !== 'published') {
        return false;
    }

    $table = community_latest_table();
    $title = !empty($post['is_secret']) ? '비밀글입니다.' : $post['title'];

    $result = (bool) sql_query_prepared(
        " insert into {$table}
            set scope = 'board',
                board_id = :board_id,
                post_id = :post_id,
                title = :title,
                mb_id = :mb_id,
                comment_count = :comment_count,
                created_at = :created_at,
                last_activity_at = :last_activity_at,
                updated_at = :updated_at
          on duplicate key update
                title = values(title),
                mb_id = values(mb_id),
                comment_count = values(comment_count),
                last_activity_at = values(last_activity_at),
                updated_at = values(updated_at) ",
        array(
            'board_id' => $board['board_id'],
            'post_id' => (int) $post['post_id'],
            'title' => $title,
            'mb_id' => $post['mb_id'],
            'comment_count' => (int) $post['comment_count'],
            'created_at' => $post['created_at'],
            'last_activity_at' => $post['last_activity_at'],
            'updated_at' => G5_TIME_YMDHIS,
        ),
        false
    );

    if ($result) {
        community_cache_delete_group('community:latest:');
    }

    return $result;
}

function community_delete_latest_post($board_id, $post_id)
{
    $table = community_latest_table();

    $result = (bool) sql_query_prepared(
        " delete from {$table}
          where scope = 'board' and board_id = :board_id and post_id = :post_id ",
        array(
            'board_id' => $board_id,
            'post_id' => (int) $post_id,
        ),
        false
    );

    if ($result) {
        community_cache_delete_group('community:latest:');
    }

    return $result;
}

function community_delete_latest_board($board_id)
{
    $table = community_latest_table();

    $result = (bool) sql_query_prepared(
        " delete from {$table}
          where scope = 'board' and board_id = :board_id ",
        array('board_id' => $board_id),
        false
    );

    if ($result) {
        community_cache_delete_group('community:latest:');
    }

    return $result;
}

function community_rebuild_latest_board(array $board)
{
    if (empty($board['board_id'])) {
        return false;
    }

    if (!community_delete_latest_board($board['board_id'])) {
        return false;
    }

    if (empty($board['use_latest']) || $board['status'] !== 'active') {
        return true;
    }

    $post_table = community_post_table();
    $posts = sql_fetch_all_prepared(
        " select * from {$post_table}
          where board_id = :board_id and status = 'published'
          order by last_activity_at desc, post_id desc ",
        array('board_id' => $board['board_id'])
    );

    foreach ($posts as $post) {
        if (!community_upsert_latest_post($board, $post)) {
            return false;
        }
    }

    return true;
}

function community_fetch_latest_posts($board_id = '', $limit = 10)
{
    $table = community_latest_table();
    $params = array('page_rows' => max(1, min(50, (int) $limit)));
    $cache_key = 'community:latest:' . ($board_id !== '' ? 'board:' . $board_id : 'all') . ':' . $params['page_rows'];
    $cached_rows = community_cache_get($cache_key);

    if (is_array($cached_rows)) {
        return $cached_rows;
    }

    $where = " where scope = 'board' ";

    if ($board_id !== '') {
        $where .= " and board_id = :board_id ";
        $params['board_id'] = $board_id;
    }

    $rows = sql_fetch_all_prepared(
        " select * from {$table} {$where}
          order by last_activity_at desc, post_id desc
          limit :page_rows ",
        $params
    );

    community_cache_set($cache_key, $rows, 60);

    return $rows;
}

function community_build_latest_items($board_id, $limit, array $member)
{
    $items = array();

    foreach (community_fetch_latest_posts($board_id, $limit) as $row) {
        $board = community_fetch_board($row['board_id']);
        if (empty($board['board_id']) || !community_can_read_board($board, $member)) {
            continue;
        }

        $items[] = array(
            'board_name_text' => get_text($board['name']),
            'title_text' => get_text($row['title']),
            'author_text' => get_text($row['mb_id']),
            'comment_count_text' => (int) $row['comment_count'],
            'date_text' => get_text(substr($row['last_activity_at'], 0, 16)),
            'is_new' => community_is_new_datetime($row['created_at']),
            'view_url_attr' => community_escape_attr(G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($row['board_id']) . '&post_id=' . (int) $row['post_id']),
        );
    }

    return $items;
}
