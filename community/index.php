<?php
include_once './_common.php';

$g5['title'] = '커뮤니티';
$community_boards = community_fetch_board_list(false);

include_once G5_PATH . '/head.php';
include_once G5_COMMUNITY_VIEW_PATH . '/basic/index.skin.php';
include_once G5_PATH . '/tail.php';
