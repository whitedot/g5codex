<?php
include_once './_common.php';

if (!check_token()) {
    alert('올바른 방법으로 이용해 주십시오.');
}

$community_comment_request = community_read_comment_save_request(g5_get_runtime_post_input());
$community_board = community_fetch_board($community_comment_request['board_id']);

if (empty($community_board['board_id'])) {
    alert('존재하지 않는 커뮤니티 게시판입니다.', G5_COMMUNITY_URL);
}

$community_post = community_fetch_post_in_board($community_board['board_id'], $community_comment_request['post_id']);
if (empty($community_post['post_id'])) {
    alert('존재하지 않는 게시글입니다.', G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($community_board['board_id']));
}

if (!community_can_read_board($community_board, $member) || !community_can_view_secret_post($community_post, $member, $is_admin)) {
    alert('게시글을 열람할 권한이 없습니다.', G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($community_board['board_id']));
}

if (!community_can_comment_board($community_board, $member)) {
    alert('댓글을 작성할 권한이 없습니다.', G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($community_board['board_id']) . '&post_id=' . (int) $community_post['post_id']);
}

if ($community_comment_request['content'] === '') {
    alert('댓글 내용을 입력하세요.', G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($community_board['board_id']) . '&post_id=' . (int) $community_post['post_id']);
}

$community_comment_id = community_insert_comment(array(
    'post_id' => $community_post['post_id'],
    'mb_id' => $member['mb_id'],
    'content' => $community_comment_request['content'],
));

if (!$community_comment_id) {
    alert('댓글을 저장하지 못했습니다.', G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($community_board['board_id']) . '&post_id=' . (int) $community_post['post_id']);
}

community_increment_post_comment_count($community_post['post_id']);

$community_post = community_fetch_post_in_board($community_board['board_id'], $community_post['post_id']);
community_upsert_latest_post($community_board, $community_post);

$community_comment = community_fetch_comment($community_comment_id);
community_notify_comment_created($community_board, $community_post, $community_comment, $member['mb_id']);

goto_url(G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($community_board['board_id']) . '&post_id=' . (int) $community_post['post_id']);
