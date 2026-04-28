<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_point_wallet_table()
{
    global $g5;

    return $g5['community_point_wallet_table'];
}

function community_point_ledger_table()
{
    global $g5;

    return $g5['community_point_ledger_table'];
}

function community_point_available_table()
{
    global $g5;

    return $g5['community_point_available_table'];
}

function community_point_fetch_wallet($mb_id)
{
    $table = community_point_wallet_table();

    return sql_fetch_prepared(" select * from {$table} where mb_id = :mb_id ", array('mb_id' => $mb_id));
}

function community_point_ensure_wallet($mb_id)
{
    $table = community_point_wallet_table();
    $wallet = community_point_fetch_wallet($mb_id);
    if (!empty($wallet['mb_id'])) {
        return $wallet;
    }

    sql_query_prepared(
        " insert into {$table}
            set mb_id = :mb_id,
                balance = 0,
                earned_total = 0,
                spent_total = 0,
                expired_total = 0,
                updated_at = :updated_at ",
        array(
            'mb_id' => $mb_id,
            'updated_at' => G5_TIME_YMDHIS,
        ),
        false
    );

    return community_point_fetch_wallet($mb_id);
}

function community_point_has_ledger($mb_id, $reason, $target_type, $target_id)
{
    $table = community_point_ledger_table();
    $row = sql_fetch_prepared(
        " select ledger_id from {$table}
          where mb_id = :mb_id and reason = :reason and target_type = :target_type and target_id = :target_id
          limit 1 ",
        array(
            'mb_id' => $mb_id,
            'reason' => $reason,
            'target_type' => $target_type,
            'target_id' => (int) $target_id,
        )
    );

    return !empty($row['ledger_id']);
}

function community_point_insert_ledger($mb_id, $amount, $balance_after, array $meta)
{
    $table = community_point_ledger_table();

    sql_query_prepared(
        " insert into {$table}
            set mb_id = :mb_id,
                amount = :amount,
                balance_after = :balance_after,
                reason = :reason,
                target_type = :target_type,
                target_id = :target_id,
                expires_at = :expires_at,
                created_by = :created_by,
                created_at = :created_at ",
        array(
            'mb_id' => $mb_id,
            'amount' => (int) $amount,
            'balance_after' => (int) $balance_after,
            'reason' => isset($meta['reason']) ? $meta['reason'] : '',
            'target_type' => isset($meta['target_type']) ? $meta['target_type'] : '',
            'target_id' => isset($meta['target_id']) ? (int) $meta['target_id'] : 0,
            'expires_at' => !empty($meta['expires_at']) ? $meta['expires_at'] : '0000-00-00 00:00:00',
            'created_by' => isset($meta['created_by']) ? $meta['created_by'] : '',
            'created_at' => G5_TIME_YMDHIS,
        ),
        false
    );

    return sql_insert_id();
}

function community_point_insert_available($mb_id, $ledger_id, $amount, $expires_at = '')
{
    if ($amount <= 0) {
        return true;
    }

    $table = community_point_available_table();

    return (bool) sql_query_prepared(
        " insert into {$table}
            set mb_id = :mb_id,
                source_ledger_id = :source_ledger_id,
                amount_total = :amount_total,
                amount_remaining = :amount_remaining,
                expires_at = :expires_at,
                created_at = :created_at ",
        array(
            'mb_id' => $mb_id,
            'source_ledger_id' => (int) $ledger_id,
            'amount_total' => (int) $amount,
            'amount_remaining' => (int) $amount,
            'expires_at' => $expires_at !== '' ? $expires_at : '0000-00-00 00:00:00',
            'created_at' => G5_TIME_YMDHIS,
        ),
        false
    );
}

function community_point_update_wallet_totals($mb_id, $amount)
{
    $table = community_point_wallet_table();

    if ($amount >= 0) {
        $sql = " update {$table}
                    set balance = balance + :amount,
                        earned_total = earned_total + :amount,
                        updated_at = :updated_at
                  where mb_id = :mb_id ";
        $params = array('amount' => (int) $amount, 'updated_at' => G5_TIME_YMDHIS, 'mb_id' => $mb_id);
    } else {
        $sql = " update {$table}
                    set balance = balance + :amount,
                        spent_total = spent_total + :spent_amount,
                        updated_at = :updated_at
                  where mb_id = :mb_id ";
        $params = array('amount' => (int) $amount, 'spent_amount' => abs((int) $amount), 'updated_at' => G5_TIME_YMDHIS, 'mb_id' => $mb_id);
    }

    return (bool) sql_query_prepared($sql, $params, false);
}

function community_point_update_wallet_expired($mb_id, $amount)
{
    $amount = abs((int) $amount);
    if ($amount < 1) {
        return true;
    }

    $table = community_point_wallet_table();

    return (bool) sql_query_prepared(
        " update {$table}
             set balance = balance - :amount,
                 expired_total = expired_total + :amount,
                 updated_at = :updated_at
           where mb_id = :mb_id ",
        array(
            'amount' => $amount,
            'updated_at' => G5_TIME_YMDHIS,
            'mb_id' => $mb_id,
        ),
        false
    );
}

function community_point_expire_available($mb_id = '', $now = '')
{
    $now = $now !== '' ? $now : G5_TIME_YMDHIS;
    $where = " where amount_remaining > 0
                 and expires_at <> '0000-00-00 00:00:00'
                 and expires_at < :now ";
    $params = array('now' => $now);

    if ($mb_id !== '') {
        $where .= " and mb_id = :mb_id ";
        $params['mb_id'] = $mb_id;
    }

    $available_table = community_point_available_table();
    $rows = sql_fetch_all_prepared(
        " select *
            from {$available_table}
            {$where}
           order by expires_at asc, available_id asc ",
        $params
    );

    $expired_count = 0;
    $expired_amount = 0;

    foreach ($rows as $row) {
        $remaining = (int) $row['amount_remaining'];
        if ($remaining < 1) {
            continue;
        }

        $wallet = community_point_ensure_wallet($row['mb_id']);
        $balance_after = (int) $wallet['balance'] - $remaining;

        sql_query_prepared(
            " update {$available_table}
                 set amount_remaining = 0
               where available_id = :available_id
                 and amount_remaining = :amount_remaining ",
            array(
                'available_id' => (int) $row['available_id'],
                'amount_remaining' => $remaining,
            ),
            false
        );

        community_point_insert_ledger($row['mb_id'], -$remaining, $balance_after, array(
            'reason' => 'expire',
            'target_type' => 'available',
            'target_id' => (int) $row['available_id'],
            'expires_at' => $row['expires_at'],
            'created_by' => 'system',
        ));
        community_point_update_wallet_expired($row['mb_id'], $remaining);

        $expired_count++;
        $expired_amount += $remaining;
    }

    return array(
        'expired_count' => $expired_count,
        'expired_amount' => $expired_amount,
    );
}

function community_point_consume_available($mb_id, $amount)
{
    $amount = abs((int) $amount);
    if ($amount < 1) {
        return true;
    }

    $table = community_point_available_table();
    $rows = sql_fetch_all_prepared(
        " select * from {$table}
          where mb_id = :mb_id
            and amount_remaining > 0
            and (expires_at = '0000-00-00 00:00:00' or expires_at >= :now)
          order by case when expires_at = '0000-00-00 00:00:00' then 1 else 0 end asc,
                   expires_at asc,
                   available_id asc ",
        array(
            'mb_id' => $mb_id,
            'now' => G5_TIME_YMDHIS,
        )
    );

    foreach ($rows as $row) {
        if ($amount <= 0) {
            break;
        }

        $consume = min($amount, (int) $row['amount_remaining']);
        sql_query_prepared(
            " update {$table}
                 set amount_remaining = amount_remaining - :consume
               where available_id = :available_id ",
            array(
                'consume' => $consume,
                'available_id' => (int) $row['available_id'],
            ),
            false
        );
        $amount -= $consume;
    }

    return $amount === 0;
}

function community_point_grant($mb_id, $amount, array $meta)
{
    $amount = (int) $amount;
    if ($mb_id === '' || $amount <= 0) {
        return array('error' => '', 'ledger_id' => 0, 'skipped' => true);
    }

    $reason = isset($meta['reason']) ? $meta['reason'] : '';
    $target_type = isset($meta['target_type']) ? $meta['target_type'] : '';
    $target_id = isset($meta['target_id']) ? (int) $meta['target_id'] : 0;

    if ($target_id > 0 && community_point_has_ledger($mb_id, $reason, $target_type, $target_id)) {
        return array('error' => '', 'ledger_id' => 0, 'skipped' => true);
    }

    community_point_expire_available($mb_id);

    $wallet = community_point_ensure_wallet($mb_id);
    $balance_after = (int) $wallet['balance'] + $amount;
    $ledger_id = community_point_insert_ledger($mb_id, $amount, $balance_after, $meta);
    community_point_insert_available($mb_id, $ledger_id, $amount, isset($meta['expires_at']) ? $meta['expires_at'] : '');
    community_point_update_wallet_totals($mb_id, $amount);

    return array('error' => '', 'ledger_id' => $ledger_id, 'skipped' => false);
}

function community_point_adjust($mb_id, $amount, array $meta)
{
    $amount = (int) $amount;
    if ($mb_id === '' || $amount === 0) {
        return array('error' => '회원 ID와 조정 포인트를 입력하세요.', 'ledger_id' => 0);
    }

    community_point_expire_available($mb_id);

    $wallet = community_point_ensure_wallet($mb_id);
    $balance_after = (int) $wallet['balance'] + $amount;
    if ($balance_after < 0) {
        return array('error' => '잔액보다 큰 포인트를 차감할 수 없습니다.', 'ledger_id' => 0);
    }

    if ($amount < 0 && !community_point_consume_available($mb_id, abs($amount))) {
        return array('error' => '사용 가능 포인트가 부족합니다.', 'ledger_id' => 0);
    }

    $ledger_id = community_point_insert_ledger($mb_id, $amount, $balance_after, $meta);
    if ($amount > 0) {
        community_point_insert_available($mb_id, $ledger_id, $amount, isset($meta['expires_at']) ? $meta['expires_at'] : '');
    }
    community_point_update_wallet_totals($mb_id, $amount);

    return array('error' => '', 'ledger_id' => $ledger_id);
}

function community_point_grant_for_post(array $board, array $post)
{
    if (empty($board['use_point']) || (int) $board['point_write'] <= 0) {
        return;
    }

    community_point_grant($post['mb_id'], (int) $board['point_write'], array(
        'reason' => 'write',
        'target_type' => 'post',
        'target_id' => (int) $post['post_id'],
    ));
}

function community_point_grant_for_comment(array $board, array $comment)
{
    if (empty($board['use_point']) || (int) $board['point_comment'] <= 0) {
        return;
    }

    community_point_grant($comment['mb_id'], (int) $board['point_comment'], array(
        'reason' => 'comment',
        'target_type' => 'comment',
        'target_id' => (int) $comment['comment_id'],
    ));
}

function community_point_apply_for_read(array $board, array $post, array $member)
{
    if (empty($board['use_point']) || (int) $board['point_read'] === 0) {
        return array('error' => '', 'skipped' => true);
    }

    $mb_id = isset($member['mb_id']) ? (string) $member['mb_id'] : '';
    if ($mb_id === '' || $mb_id === (string) $post['mb_id']) {
        return array('error' => '', 'skipped' => true);
    }

    $amount = (int) $board['point_read'];
    $meta = array(
        'reason' => 'read',
        'target_type' => 'post',
        'target_id' => (int) $post['post_id'],
        'created_by' => $mb_id,
    );

    if (community_point_has_ledger($mb_id, $meta['reason'], $meta['target_type'], $meta['target_id'])) {
        return array('error' => '', 'skipped' => true);
    }

    if ($amount > 0) {
        return community_point_grant($mb_id, $amount, $meta);
    }

    $result = community_point_adjust($mb_id, $amount, $meta);
    if ($result['error'] !== '') {
        return array('error' => $result['error'], 'skipped' => false);
    }

    return array('error' => '', 'ledger_id' => $result['ledger_id'], 'skipped' => false);
}
