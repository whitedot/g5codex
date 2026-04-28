<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_comment_table()
{
    global $g5;

    return $g5['community_comment_table'];
}

function community_fetch_comment($comment_id)
{
    $table = community_comment_table();

    return sql_fetch_prepared(
        " select * from {$table} where comment_id = :comment_id and status <> 'deleted' ",
        array('comment_id' => (int) $comment_id)
    );
}

function community_fetch_post_comments($post_id)
{
    $table = community_comment_table();

    return sql_fetch_all_prepared(
        " select * from {$table}
          where post_id = :post_id and status = 'published'
          order by comment_id asc ",
        array('post_id' => (int) $post_id)
    );
}

function community_insert_comment(array $payload)
{
    $table = community_comment_table();
    $now = G5_TIME_YMDHIS;
    $params = array(
        'post_id' => (int) community_payload_value($payload, 'post_id', 0),
        'parent_id' => (int) community_payload_value($payload, 'parent_id', 0),
        'mb_id' => (string) community_payload_value($payload, 'mb_id', ''),
        'content' => (string) community_payload_value($payload, 'content', ''),
        'created_at' => $now,
        'updated_at' => $now,
    );

    if ($params['post_id'] < 1 || $params['mb_id'] === '' || $params['content'] === '') {
        return 0;
    }

    $sql = " insert into {$table}
                set post_id = :post_id,
                    parent_id = :parent_id,
                    mb_id = :mb_id,
                    content = :content,
                    status = 'published',
                    created_at = :created_at,
                    updated_at = :updated_at ";

    if (!sql_query_prepared($sql, $params, false)) {
        return 0;
    }

    return sql_insert_id();
}

function community_increment_post_comment_count($post_id)
{
    $table = community_post_table();

    return (bool) sql_query_prepared(
        " update {$table}
             set comment_count = comment_count + 1,
                 last_activity_at = :last_activity_at,
                 updated_at = :updated_at
           where post_id = :post_id and status <> 'deleted' ",
        array(
            'post_id' => (int) $post_id,
            'last_activity_at' => G5_TIME_YMDHIS,
            'updated_at' => G5_TIME_YMDHIS,
        ),
        false
    );
}

function community_decrement_post_comment_count($post_id)
{
    return community_recalculate_post_comment_summary($post_id);
}

function community_recalculate_post_comment_summary($post_id)
{
    $table = community_post_table();
    $comment_table = community_comment_table();
    $summary = sql_fetch_prepared(
        " select p.created_at as post_created_at,
                 count(c.comment_id) as comment_count,
                 max(c.created_at) as last_comment_at
            from {$table} p
            left join {$comment_table} c on c.post_id = p.post_id and c.status = 'published'
           where p.post_id = :post_id
             and p.status <> 'deleted'
           group by p.post_id ",
        array('post_id' => (int) $post_id)
    );

    if (empty($summary['post_created_at'])) {
        return false;
    }

    $last_activity_at = !empty($summary['last_comment_at']) ? $summary['last_comment_at'] : $summary['post_created_at'];

    return (bool) sql_query_prepared(
        " update {$table}
             set comment_count = :comment_count,
                 last_activity_at = :last_activity_at,
                 updated_at = :updated_at
           where post_id = :post_id and status <> 'deleted' ",
        array(
            'post_id' => (int) $post_id,
            'comment_count' => (int) $summary['comment_count'],
            'last_activity_at' => $last_activity_at,
            'updated_at' => G5_TIME_YMDHIS,
        ),
        false
    );
}

function community_soft_delete_comment($comment_id)
{
    $table = community_comment_table();

    return (bool) sql_query_prepared(
        " update {$table}
             set status = 'deleted',
                 updated_at = :updated_at,
                 deleted_at = :deleted_at
           where comment_id = :comment_id and status <> 'deleted' ",
        array(
            'comment_id' => (int) $comment_id,
            'updated_at' => G5_TIME_YMDHIS,
            'deleted_at' => G5_TIME_YMDHIS,
        ),
        false
    );
}
