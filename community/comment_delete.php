<?php
include_once './_common.php';

if (!check_token()) {
    alert('올바른 방법으로 이용해 주십시오.');
}

$community_comment_delete_request = community_read_comment_delete_request(g5_get_runtime_post_input());
$community_board = community_fetch_board($community_comment_delete_request['board_id'], true);

if (empty($community_board['board_id'])) {
    alert('존재하지 않는 커뮤니티 게시판입니다.', G5_COMMUNITY_URL);
}

$community_post = community_fetch_post_in_board($community_board['board_id'], $community_comment_delete_request['post_id']);
if (empty($community_post['post_id'])) {
    alert('존재하지 않는 게시글입니다.', G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($community_board['board_id']));
}

$community_comment = community_fetch_comment($community_comment_delete_request['comment_id']);
if (empty($community_comment['comment_id']) || (int) $community_comment['post_id'] !== (int) $community_post['post_id']) {
    alert('존재하지 않는 댓글입니다.', G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($community_board['board_id']) . '&post_id=' . (int) $community_post['post_id']);
}

if (!community_can_edit_comment($community_comment, $member, $is_admin)) {
    alert('댓글을 삭제할 권한이 없습니다.', G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($community_board['board_id']) . '&post_id=' . (int) $community_post['post_id']);
}

if (!community_soft_delete_comment($community_comment['comment_id'])) {
    alert('댓글을 삭제하지 못했습니다.', G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($community_board['board_id']) . '&post_id=' . (int) $community_post['post_id']);
}

community_decrement_post_comment_count($community_post['post_id']);
$community_post = community_fetch_post_in_board($community_board['board_id'], $community_post['post_id']);
community_upsert_latest_post($community_board, $community_post);

goto_url(G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($community_board['board_id']) . '&post_id=' . (int) $community_post['post_id']);
