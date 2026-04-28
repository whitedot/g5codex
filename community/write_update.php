<?php
include_once './_common.php';

if (!check_token()) {
    alert('올바른 방법으로 이용해 주십시오.');
}

$community_save_request = community_read_save_request(g5_get_runtime_post_input());
$community_board = community_fetch_board($community_save_request['board_id']);

if (empty($community_board['board_id'])) {
    alert('존재하지 않는 커뮤니티 게시판입니다.', G5_COMMUNITY_URL);
}

if (!community_can_write_board($community_board, $member)) {
    alert('게시글을 작성할 권한이 없습니다.', G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($community_board['board_id']));
}

if ($community_save_request['title'] === '') {
    alert('제목을 입력하세요.', G5_COMMUNITY_URL . '/write.php?board_id=' . rawurlencode($community_board['board_id']));
}

if ($community_save_request['content'] === '') {
    alert('내용을 입력하세요.', G5_COMMUNITY_URL . '/write.php?board_id=' . rawurlencode($community_board['board_id']));
}

if (empty($community_board['use_category'])) {
    $community_save_request['category_id'] = 0;
} elseif ($community_save_request['category_id'] > 0) {
    $community_category = community_fetch_board_category($community_board['board_id'], $community_save_request['category_id']);
    if (empty($community_category['category_id'])) {
        alert('카테고리가 올바르지 않습니다.', G5_COMMUNITY_URL . '/write.php?board_id=' . rawurlencode($community_board['board_id']));
    }
}

$community_payload = array(
    'board_id' => $community_board['board_id'],
    'category_id' => $community_save_request['category_id'],
    'mb_id' => isset($member['mb_id']) ? $member['mb_id'] : '',
    'title' => $community_save_request['title'],
    'content' => $community_save_request['content'],
    'content_format' => 'text',
    'summary' => cut_str(strip_tags($community_save_request['content']), 120),
    'is_secret' => $community_save_request['is_secret'],
    'is_notice' => $is_admin ? $community_save_request['is_notice'] : 0,
    'notice_order' => $is_admin ? $community_save_request['notice_order'] : 0,
);

if ($community_save_request['post_id'] > 0) {
    $community_post = community_fetch_post_in_board($community_board['board_id'], $community_save_request['post_id']);
    if (empty($community_post['post_id'])) {
        alert('존재하지 않는 게시글입니다.', G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($community_board['board_id']));
    }
    if (!community_can_edit_post($community_post, $member, $is_admin)) {
        alert('게시글을 수정할 권한이 없습니다.', G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($community_board['board_id']) . '&post_id=' . (int) $community_post['post_id']);
    }
    if (!$is_admin) {
        $community_payload['is_notice'] = (int) $community_post['is_notice'];
        $community_payload['notice_order'] = (int) $community_post['notice_order'];
    }
    if (!community_update_post($community_save_request['post_id'], $community_payload)) {
        alert('게시글을 저장하지 못했습니다.', G5_COMMUNITY_URL . '/write.php?board_id=' . rawurlencode($community_board['board_id']) . '&post_id=' . (int) $community_save_request['post_id']);
    }
    $community_post_id = $community_save_request['post_id'];
} else {
    $community_post_id = community_insert_post($community_payload);
    if (!$community_post_id) {
        alert('게시글을 저장하지 못했습니다.', G5_COMMUNITY_URL . '/write.php?board_id=' . rawurlencode($community_board['board_id']));
    }
}

$community_saved_post = community_fetch_post_in_board($community_board['board_id'], $community_post_id);
community_upsert_latest_post($community_board, $community_saved_post);

goto_url(G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($community_board['board_id']) . '&post_id=' . (int) $community_post_id);
