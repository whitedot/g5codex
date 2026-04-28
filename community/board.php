<?php
include_once './_common.php';

$community_list_request = community_read_list_request(g5_get_runtime_get_input(), $config);
$community_board = community_fetch_board($community_list_request['board_id']);

if (empty($community_board['board_id'])) {
    alert('존재하지 않는 커뮤니티 게시판입니다.', G5_COMMUNITY_URL);
}

if (!community_can_read_board($community_board, $member)) {
    alert('게시판을 열람할 권한이 없습니다.', G5_URL);
}

if (empty($community_board['use_category'])) {
    $community_list_request['category_id'] = 0;
} elseif ($community_list_request['category_id'] > 0) {
    $community_category = community_fetch_board_category($community_board['board_id'], $community_list_request['category_id']);
    if (empty($community_category['category_id'])) {
        $community_list_request['category_id'] = 0;
    }
}

$community_list_view = community_build_list_view($community_list_request, $community_board, $member, $is_admin);
$g5['title'] = $community_list_view['title'];

include_once G5_PATH . '/head.php';
include_once G5_COMMUNITY_VIEW_PATH . '/basic/board.skin.php';
include_once G5_PATH . '/tail.php';
