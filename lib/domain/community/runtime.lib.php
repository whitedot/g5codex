<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_register_runtime_tables()
{
    global $g5;

    if (!defined('G5_TABLE_PREFIX')) {
        return;
    }

    $tables = array(
        'community_board_table' => 'community_board',
        'community_board_category_table' => 'community_board_category',
        'community_post_table' => 'community_post',
        'community_comment_table' => 'community_comment',
        'community_latest_table' => 'community_latest_index',
        'community_point_ledger_table' => 'community_point_ledger',
        'community_point_available_table' => 'community_point_available',
        'community_point_wallet_table' => 'community_point_wallet',
        'community_attachment_table' => 'community_attachment',
        'community_scrap_table' => 'community_scrap',
    );

    foreach ($tables as $key => $suffix) {
        if (empty($g5[$key])) {
            $g5[$key] = G5_TABLE_PREFIX . $suffix;
        }
    }
}

community_register_runtime_tables();
