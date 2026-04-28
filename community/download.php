<?php
include_once './_common.php';

$community_download_request = community_read_view_request(g5_get_runtime_get_input());
$community_attachment_id = max(0, (int) community_read_scalar(g5_get_runtime_get_input(), 'attachment_id', 0));
$community_board = community_fetch_board($community_download_request['board_id']);

if (empty($community_board['board_id'])) {
    alert('존재하지 않는 커뮤니티 게시판입니다.', G5_COMMUNITY_URL);
}

if (!community_can_read_board($community_board, $member)) {
    alert('게시판을 열람할 권한이 없습니다.', G5_URL);
}

$community_post = community_fetch_post_in_board($community_board['board_id'], $community_download_request['post_id']);
if (empty($community_post['post_id'])) {
    alert('존재하지 않는 게시글입니다.', G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($community_board['board_id']));
}

if (!community_can_view_secret_post($community_post, $member, $is_admin)) {
    alert('첨부파일을 다운로드할 권한이 없습니다.', G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($community_board['board_id']));
}

$community_attachment = community_fetch_attachment($community_attachment_id);
if (empty($community_attachment['attachment_id']) || (int) $community_attachment['post_id'] !== (int) $community_post['post_id']) {
    alert('존재하지 않는 첨부파일입니다.', G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($community_board['board_id']) . '&post_id=' . (int) $community_post['post_id']);
}

$community_file_path = community_attachment_absolute_path($community_attachment);
if (!is_file($community_file_path)) {
    alert('첨부파일을 찾을 수 없습니다.', G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($community_board['board_id']) . '&post_id=' . (int) $community_post['post_id']);
}

$community_file_name = get_safe_filename($community_attachment['original_name']);

while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/octet-stream');
header('Content-Length: ' . filesize($community_file_path));
header('Content-Disposition: attachment; filename="' . rawurlencode($community_file_name) . '"');
header('Cache-Control: private, no-transform, no-store, must-revalidate');
readfile($community_file_path);
exit;
