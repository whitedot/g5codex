<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

$post_url = G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($board['board_id']) . '&post_id=' . (int) $post['post_id'];
?>
<div>
    <h1><?php echo get_text($board['name']); ?> 댓글 알림</h1>
    <p><strong><?php echo get_text($post['title']); ?></strong> 글에 새 댓글이 등록되었습니다.</p>
    <p><?php echo nl2br(get_text(cut_str($comment['content'], 300))); ?></p>
    <p><a href="<?php echo community_escape_attr($post_url); ?>" target="_blank">게시글 보기</a></p>
</div>
