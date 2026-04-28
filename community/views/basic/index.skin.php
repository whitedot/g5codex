<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<section class="community-home">
    <h2>커뮤니티</h2>

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
</section>
