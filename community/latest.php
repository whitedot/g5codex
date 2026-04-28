<?php
include_once './_common.php';

$community_latest_board_id = community_read_board_id(g5_get_runtime_get_input());
$community_latest_limit = max(1, min(50, (int) community_read_scalar(g5_get_runtime_get_input(), 'rows', 20)));
$community_latest_board = array();

if ($community_latest_board_id !== '') {
    $community_latest_board = community_fetch_board($community_latest_board_id);
    if (empty($community_latest_board['board_id'])) {
        alert('존재하지 않는 커뮤니티 게시판입니다.', G5_COMMUNITY_URL);
    }
    if (!community_can_read_board($community_latest_board, $member)) {
        alert('게시판을 열람할 권한이 없습니다.', G5_COMMUNITY_URL);
    }
}

$community_latest_items = community_build_latest_items($community_latest_board_id, $community_latest_limit, $member);
$g5['title'] = !empty($community_latest_board['name']) ? $community_latest_board['name'] . ' 최신글' : '커뮤니티 최신글';

include_once G5_PATH . '/head.php';
include_once G5_COMMUNITY_VIEW_PATH . '/basic/latest.skin.php';
include_once G5_PATH . '/tail.php';
