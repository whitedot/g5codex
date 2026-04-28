<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<section class="community-board">
    <div class="community-board-head">
        <h2><?php echo $community_list_view['board_name_text']; ?></h2>
        <?php if ($community_list_view['description_text'] !== '') { ?>
            <p><?php echo $community_list_view['description_text']; ?></p>
        <?php } ?>
    </div>

    <?php if (!empty($community_list_view['category_options'])) { ?>
        <form method="get" action="<?php echo $community_list_view['category_action_attr']; ?>" class="community-category-filter">
            <input type="hidden" name="board_id" value="<?php echo $community_list_view['board_id_attr']; ?>">
            <select name="category_id">
                <option value="0">전체</option>
                <?php foreach ($community_list_view['category_options'] as $option) { ?>
                    <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                <?php } ?>
            </select>
            <button type="submit">보기</button>
        </form>
    <?php } ?>

    <table class="community-post-table">
        <caption><?php echo $community_list_view['board_name_text']; ?> 게시글 목록</caption>
        <thead>
        <tr>
            <th scope="col">번호</th>
            <th scope="col">제목</th>
            <th scope="col">작성자</th>
            <th scope="col">작성일</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($community_list_view['items'] as $item) { ?>
            <tr<?php echo $item['is_notice'] ? ' class="is-notice"' : ''; ?>>
                <td><?php echo $item['is_notice'] ? '공지' : $item['post_id_text']; ?></td>
                <td>
                    <a href="<?php echo $item['view_url_attr']; ?>"><?php echo $item['title_text']; ?></a>
                    <?php if ($item['is_new']) { ?><span>새글</span><?php } ?>
                    <?php if ($item['is_secret']) { ?><span>비밀</span><?php } ?>
                    <?php if ($item['comment_count_text'] > 0) { ?><span><?php echo $item['comment_count_text']; ?></span><?php } ?>
                </td>
                <td><?php echo $item['author_text']; ?></td>
                <td><?php echo $item['date_text']; ?></td>
            </tr>
        <?php } ?>
        <?php if (empty($community_list_view['items'])) { ?>
            <tr><td colspan="4"><?php echo $community_list_view['empty_message']; ?></td></tr>
        <?php } ?>
        </tbody>
    </table>

    <div class="community-actions">
        <?php if ($community_list_view['can_write']) { ?>
            <a href="<?php echo $community_list_view['write_url_attr']; ?>">글쓰기</a>
        <?php } ?>
    </div>

    <?php echo $community_list_view['paging_html']; ?>
</section>
