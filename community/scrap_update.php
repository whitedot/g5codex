<?php
include_once './_common.php';

if (!check_token()) {
    alert('올바른 방법으로 이용해 주십시오.');
}

if (empty($member['mb_id'])) {
    alert('로그인 후 이용해 주십시오.', G5_COMMUNITY_URL);
}

$community_scrap_request = community_read_scrap_update_request(g5_get_runtime_post_input());
$community_board = community_fetch_board($community_scrap_request['board_id']);
if (empty($community_board['board_id'])) {
    alert('존재하지 않는 커뮤니티 게시판입니다.', G5_COMMUNITY_URL);
}

if (!community_can_read_board($community_board, $member)) {
    alert('게시판을 열람할 권한이 없습니다.', G5_COMMUNITY_URL);
}

$community_post = community_fetch_post_in_board($community_board['board_id'], $community_scrap_request['post_id']);
if (empty($community_post['post_id'])) {
    alert('존재하지 않는 게시글입니다.', G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($community_board['board_id']));
}

if (!community_can_view_secret_post($community_post, $member, $is_admin)) {
    alert('스크랩할 권한이 없습니다.', G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($community_board['board_id']));
}

$community_scrap_result = community_toggle_scrap($member['mb_id'], $community_post);
$community_return_url = G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($community_board['board_id']) . '&post_id=' . (int) $community_post['post_id'];
if ($community_scrap_result['error'] !== '') {
    alert($community_scrap_result['error'], $community_return_url);
}

goto_url($community_return_url);
