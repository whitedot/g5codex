<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<section class="community-latest">
    <header>
        <h2><?php echo get_text($g5['title']); ?></h2>
    </header>

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
