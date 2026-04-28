<?php
include_once './_common.php';

$community_form_request = community_read_form_request(g5_get_runtime_get_input());
$community_board = community_fetch_board($community_form_request['board_id']);

if (empty($community_board['board_id'])) {
    alert('존재하지 않는 커뮤니티 게시판입니다.', G5_COMMUNITY_URL);
}

if (!community_can_write_board($community_board, $member)) {
    alert('게시글을 작성할 권한이 없습니다.', G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($community_board['board_id']));
}

$community_post = array();
if ($community_form_request['post_id'] > 0) {
    $community_post = community_fetch_post_in_board($community_board['board_id'], $community_form_request['post_id']);
    if (empty($community_post['post_id'])) {
        alert('존재하지 않는 게시글입니다.', G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($community_board['board_id']));
    }
    if (!community_can_edit_post($community_post, $member, $is_admin)) {
        alert('게시글을 수정할 권한이 없습니다.', G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($community_board['board_id']) . '&post_id=' . (int) $community_post['post_id']);
    }
}

$community_form_view = community_build_form_view($community_board, $community_post, $member, $is_admin);
$g5['title'] = $community_form_view['title'];

include_once G5_PATH . '/head.php';
include_once G5_COMMUNITY_VIEW_PATH . '/basic/write.skin.php';
include_once G5_PATH . '/tail.php';
