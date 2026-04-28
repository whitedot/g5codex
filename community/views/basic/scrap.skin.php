<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<section class="community-scrap">
    <header>
        <h2><?php echo get_text($g5['title']); ?></h2>
    </header>

    <?php if (empty($community_scrap_view['items'])) { ?>
        <p><?php echo $community_scrap_view['empty_message']; ?></p>
    <?php } else { ?>
        <ul>
            <?php foreach ($community_scrap_view['items'] as $item) { ?>
                <li>
                    <a href="<?php echo $item['view_url_attr']; ?>">
                        <span><?php echo $item['board_name_text']; ?></span>
                        <strong><?php echo $item['title_text']; ?></strong>
                        <?php if ($item['comment_count_text'] > 0) { ?><span><?php echo $item['comment_count_text']; ?></span><?php } ?>
                    </a>
                    <span><?php echo $item['author_text']; ?> · 스크랩 <?php echo $item['date_text']; ?></span>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>

    <?php echo $community_scrap_view['paging_html']; ?>
</section>
