<?php
include_once './_common.php';

$community_view_request = community_read_view_request(g5_get_runtime_get_input());
$community_board = community_fetch_board($community_view_request['board_id']);

if (empty($community_board['board_id'])) {
    alert('존재하지 않는 커뮤니티 게시판입니다.', G5_COMMUNITY_URL);
}

if (!community_can_read_board($community_board, $member)) {
    alert('게시판을 열람할 권한이 없습니다.', G5_URL);
}

$community_post = community_fetch_post_in_board($community_board['board_id'], $community_view_request['post_id']);
if (empty($community_post['post_id'])) {
    alert('존재하지 않는 게시글입니다.', G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($community_board['board_id']));
}

$community_view = community_build_view_view($community_board, $community_post, $member, $is_admin);
$g5['title'] = $community_view['title'];

include_once G5_PATH . '/head.php';
include_once G5_COMMUNITY_VIEW_PATH . '/basic/view.skin.php';
include_once G5_PATH . '/tail.php';
