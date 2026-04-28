<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<article class="community-post-view">
    <header>
        <p><?php echo $community_view['board_name_text']; ?></p>
        <h2><?php echo $community_view['title_text']; ?></h2>
        <p><?php echo $community_view['author_text']; ?> · <?php echo $community_view['date_text']; ?></p>
    </header>

    <div class="community-post-content">
        <?php echo $community_view['content_html']; ?>
    </div>

    <?php if (!empty($community_view['attachments'])) { ?>
        <section class="community-attachments">
            <h3>첨부파일</h3>
            <ul>
                <?php foreach ($community_view['attachments'] as $attachment) { ?>
                    <li><a href="<?php echo $attachment['download_url_attr']; ?>"><?php echo $attachment['name_text']; ?> (<?php echo $attachment['size_text']; ?>)</a></li>
                <?php } ?>
            </ul>
        </section>
    <?php } ?>

    <section class="community-comments">
        <h3>댓글</h3>

        <?php foreach ($community_view['comments'] as $comment) { ?>
            <article class="community-comment">
                <header>
                    <strong><?php echo $comment['author_text']; ?></strong>
                    <span><?php echo $comment['date_text']; ?></span>
                </header>
                <div><?php echo $comment['content_html']; ?></div>
                <?php if ($comment['can_edit']) { ?>
                    <form method="post" action="<?php echo $community_view['comment_delete_action_attr']; ?>" onsubmit="return confirm('댓글을 삭제하시겠습니까?');">
                        <input type="hidden" name="token" value="<?php echo $community_view['token']; ?>">
                        <input type="hidden" name="board_id" value="<?php echo $community_view['board_id_attr']; ?>">
                        <input type="hidden" name="post_id" value="<?php echo $community_view['post_id_attr']; ?>">
                        <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id_attr']; ?>">
                        <button type="submit">삭제</button>
                    </form>
                <?php } ?>
            </article>
        <?php } ?>

        <?php if (empty($community_view['comments'])) { ?>
            <p>등록된 댓글이 없습니다.</p>
        <?php } ?>

        <?php if ($community_view['can_comment']) { ?>
            <form method="post" action="<?php echo $community_view['comment_action_attr']; ?>">
                <input type="hidden" name="token" value="<?php echo $community_view['token']; ?>">
                <input type="hidden" name="board_id" value="<?php echo $community_view['board_id_attr']; ?>">
                <input type="hidden" name="post_id" value="<?php echo $community_view['post_id_attr']; ?>">
                <label for="comment_content">댓글 작성</label>
                <textarea name="content" id="comment_content" rows="4" required></textarea>
                <button type="submit">댓글 등록</button>
            </form>
        <?php } ?>
    </section>

    <div class="community-actions">
        <a href="<?php echo $community_view['list_url_attr']; ?>">목록</a>
        <?php if ($community_view['can_write']) { ?>
            <a href="<?php echo $community_view['write_url_attr']; ?>">글쓰기</a>
        <?php } ?>
        <?php if ($community_view['can_edit']) { ?>
            <a href="<?php echo $community_view['edit_url_attr']; ?>">수정</a>
            <form method="post" action="<?php echo $community_view['delete_action_attr']; ?>" onsubmit="return confirm('게시글을 삭제하시겠습니까?');">
                <input type="hidden" name="token" value="<?php echo $community_view['token']; ?>">
                <input type="hidden" name="board_id" value="<?php echo $community_view['board_id_attr']; ?>">
                <input type="hidden" name="post_id" value="<?php echo $community_view['post_id_attr']; ?>">
                <button type="submit">삭제</button>
            </form>
        <?php } ?>
    </div>
</article>
