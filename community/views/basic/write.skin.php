<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<section class="community-write">
    <h2><?php echo $community_form_view['board_name_text']; ?></h2>

    <form method="post" action="<?php echo $community_form_view['form_action_attr']; ?>" enctype="multipart/form-data">
        <input type="hidden" name="token" value="<?php echo $community_form_view['token']; ?>">
        <input type="hidden" name="board_id" value="<?php echo $community_form_view['board_id_attr']; ?>">
        <input type="hidden" name="post_id" value="<?php echo $community_form_view['post_id_attr']; ?>">

        <?php if ($community_form_view['use_category'] && !empty($community_form_view['category_options'])) { ?>
            <p>
                <label for="category_id">카테고리</label>
                <select name="category_id" id="category_id">
                    <option value="0">선택 안 함</option>
                    <?php foreach ($community_form_view['category_options'] as $option) { ?>
                        <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                    <?php } ?>
                </select>
            </p>
        <?php } ?>

        <p>
            <label for="title">제목</label>
            <input type="text" name="title" id="title" value="<?php echo $community_form_view['title_value']; ?>" required maxlength="255">
        </p>

        <p>
            <label for="content">내용</label>
            <textarea name="content" id="content" rows="12" required><?php echo $community_form_view['content_value']; ?></textarea>
        </p>

        <p>
            <label><input type="checkbox" name="is_secret" value="1"<?php echo $community_form_view['is_secret_checked']; ?>> 비밀글</label>
        </p>

        <?php if ($community_form_view['is_admin']) { ?>
            <p>
                <label><input type="checkbox" name="is_notice" value="1"<?php echo $community_form_view['is_notice_checked']; ?>> 공지글</label>
                <label for="notice_order">공지 정렬</label>
                <input type="number" name="notice_order" id="notice_order" value="<?php echo $community_form_view['notice_order_value']; ?>">
                <label for="notice_started_at">공지 시작</label>
                <input type="datetime-local" name="notice_started_at" id="notice_started_at" value="<?php echo $community_form_view['notice_started_at_value']; ?>">
                <label for="notice_ended_at">공지 종료</label>
                <input type="datetime-local" name="notice_ended_at" id="notice_ended_at" value="<?php echo $community_form_view['notice_ended_at_value']; ?>">
            </p>
        <?php } ?>

        <?php if ($community_form_view['use_attachment']) { ?>
            <section class="community-attachment-form">
                <h3>첨부파일</h3>
                <?php if (!empty($community_form_view['attachments'])) { ?>
                    <ul>
                        <?php foreach ($community_form_view['attachments'] as $attachment) { ?>
                            <li>
                                <label>
                                    <input type="checkbox" name="delete_attachment[]" value="<?php echo $attachment['id_attr']; ?>">
                                    <?php echo $attachment['name_text']; ?> (<?php echo $attachment['size_text']; ?>) 삭제
                                </label>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
                <input type="file" name="attachments[]" multiple>
                <p>최대 <?php echo $community_form_view['upload_file_count']; ?>개<?php if ($community_form_view['upload_file_size'] > 0) { ?>, 파일당 <?php echo get_filesize($community_form_view['upload_file_size']); ?><?php } ?><?php if ($community_form_view['upload_extensions_text'] !== '') { ?>, 허용 확장자 <?php echo $community_form_view['upload_extensions_text']; ?><?php } ?></p>
            </section>
        <?php } ?>

        <div class="community-actions">
            <a href="<?php echo $community_form_view['list_url_attr']; ?>">목록</a>
            <button type="submit">저장</button>
        </div>
    </form>
</section>
