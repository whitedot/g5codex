<?php
include_once './_common.php';

$g5['title'] = '커뮤니티';
$community_group_id = preg_replace('/[^a-z0-9_]/i', '', community_read_scalar(g5_get_runtime_get_input(), 'group_id', ''));
$community_boards = community_fetch_board_list(false, $community_group_id);
$community_latest_items = community_build_latest_items('', 10, $member);

include_once G5_PATH . '/head.php';
include_once G5_COMMUNITY_VIEW_PATH . '/basic/index.skin.php';
include_once G5_PATH . '/tail.php';
