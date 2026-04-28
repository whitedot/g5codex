<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<section class="community-home">
    <h2>커뮤니티</h2>
    <?php if (!empty($member['mb_id'])) { ?>
        <p><a href="<?php echo community_escape_attr(G5_COMMUNITY_URL . '/scrap.php'); ?>">내 스크랩 보기</a></p>
    <?php } ?>

    <?php if (empty($community_boards)) { ?>
        <p>등록된 커뮤니티 게시판이 없습니다.</p>
    <?php } else { ?>
        <ul class="community-board-list">
            <?php foreach ($community_boards as $board) { ?>
                <li>
                    <a href="<?php echo community_escape_attr(G5_COMMUNITY_URL . '/board.php?board_id=' . rawurlencode($board['board_id'])); ?>">
                        <strong><?php echo get_text($board['name']); ?></strong>
                        <?php if ($board['description'] !== '') { ?>
                            <span><?php echo get_text($board['description']); ?></span>
                        <?php } ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>

    <section class="community-latest">
        <h3>최신글</h3>
        <p><a href="<?php echo G5_COMMUNITY_URL; ?>/latest.php">전체 최신글 보기</a></p>
        <?php if (empty($community_latest_items)) { ?>
            <p>최신글이 없습니다.</p>
        <?php } else { ?>
            <ul>
                <?php foreach ($community_latest_items as $item) { ?>
                    <li>
                        <a href="<?php echo $item['view_url_attr']; ?>">
                            <span><?php echo $item['board_name_text']; ?></span>
                            <strong><?php echo $item['title_text']; ?></strong>
                            <?php if ($item['is_new']) { ?><em>새글</em><?php } ?>
                            <?php if ($item['comment_count_text'] > 0) { ?><span><?php echo $item['comment_count_text']; ?></span><?php } ?>
                        </a>
                        <span><?php echo $item['author_text']; ?> · <?php echo $item['date_text']; ?></span>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </section>
</section>
