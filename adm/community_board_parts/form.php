<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<form method="post" action="<?php echo $community_board_form_view['form_action_attr']; ?>" class="admin-form-layout ui-form-theme ui-form-showcase">
    <input type="hidden" name="token" value="<?php echo $community_board_form_view['admin_token']; ?>">
    <input type="hidden" name="original_board_id" value="<?php echo $community_board_form_view['original_board_id_attr']; ?>">

    <section class="card">
        <div class="card-header">
            <h2 class="card-title">기본 설정</h2>
        </div>
        <div class="card-body">
            <div class="af-grid">
                <div class="af-row">
                    <div class="af-label">
                        <label for="board_id" class="form-label">게시판 ID<strong class="caption-sr-only">필수</strong></label>
                    </div>
                    <div class="af-field">
                        <input type="text" name="board_id" value="<?php echo $community_board_form_view['board_id_value']; ?>" id="board_id" required class="required form-input" maxlength="50"<?php echo $community_board_form_view['board_id_readonly_attr']; ?>>
                    </div>
                </div>

                <div class="af-row">
                    <div class="af-label">
                        <label for="name" class="form-label">게시판 이름<strong class="caption-sr-only">필수</strong></label>
                    </div>
                    <div class="af-field">
                        <input type="text" name="name" value="<?php echo $community_board_form_view['name_value']; ?>" id="name" required class="required form-input" maxlength="255">
                    </div>
                </div>

                <div class="af-row">
                    <div class="af-label">
                        <label for="description" class="form-label">설명</label>
                    </div>
                    <div class="af-field">
                        <textarea name="description" id="description" rows="4" class="form-textarea"><?php echo $community_board_form_view['description_value']; ?></textarea>
                    </div>
                </div>

                <div class="af-row">
                    <div class="af-label">
                        <label for="status" class="form-label">상태</label>
                    </div>
                    <div class="af-field">
                        <select name="status" id="status" class="form-select">
                            <?php foreach ($community_board_form_view['status_options'] as $option) { ?>
                                <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="af-row">
                    <div class="af-label">
                        <label for="list_order" class="form-label">정렬</label>
                    </div>
                    <div class="af-field">
                        <input type="number" name="list_order" value="<?php echo $community_board_form_view['list_order_value']; ?>" id="list_order" class="form-input af-input-sm">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-header">
            <h2 class="card-title">권한과 기능</h2>
        </div>
        <div class="card-body">
            <div class="af-grid">
                <div class="af-row">
                    <div class="af-label">
                        <span class="form-label">권한 레벨</span>
                    </div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label for="read_level" class="ui-form-inline-note">읽기</label>
                            <select name="read_level" id="read_level" class="form-select af-input-sm">
                                <?php foreach ($community_board_form_view['read_level_options'] as $option) { ?>
                                    <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                                <?php } ?>
                            </select>
                            <label for="write_level" class="ui-form-inline-note">쓰기</label>
                            <select name="write_level" id="write_level" class="form-select af-input-sm">
                                <?php foreach ($community_board_form_view['write_level_options'] as $option) { ?>
                                    <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                                <?php } ?>
                            </select>
                            <label for="comment_level" class="ui-form-inline-note">댓글</label>
                            <select name="comment_level" id="comment_level" class="form-select af-input-sm">
                                <?php foreach ($community_board_form_view['comment_level_options'] as $option) { ?>
                                    <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="af-row">
                    <div class="af-label">
                        <span class="form-label">사용 기능</span>
                    </div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label class="af-check form-label"><input type="checkbox" name="use_category" value="1"<?php echo $community_board_form_view['use_category_checked']; ?> class="form-checkbox"><span class="form-label">카테고리</span></label>
                            <label class="af-check form-label"><input type="checkbox" name="use_latest" value="1"<?php echo $community_board_form_view['use_latest_checked']; ?> class="form-checkbox"><span class="form-label">최신글</span></label>
                            <label class="af-check form-label"><input type="checkbox" name="use_comment" value="1"<?php echo $community_board_form_view['use_comment_checked']; ?> class="form-checkbox"><span class="form-label">댓글</span></label>
                        </div>
                    </div>
                </div>

                <div class="af-row">
                    <div class="af-label">
                        <span class="form-label">메일 알림</span>
                    </div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label class="af-check form-label"><input type="checkbox" name="use_mail_post" value="1"<?php echo $community_board_form_view['use_mail_post_checked']; ?> class="form-checkbox"><span class="form-label">게시물 작성자</span></label>
                            <label class="af-check form-label"><input type="checkbox" name="use_mail_comment" value="1"<?php echo $community_board_form_view['use_mail_comment_checked']; ?> class="form-checkbox"><span class="form-label">댓글 작성자</span></label>
                            <label class="af-check form-label"><input type="checkbox" name="mail_admin" value="1"<?php echo $community_board_form_view['mail_admin_checked']; ?> class="form-checkbox"><span class="form-label">관리자</span></label>
                        </div>
                    </div>
                </div>

                <div class="af-row">
                    <div class="af-label">
                        <label for="categories" class="form-label">카테고리</label>
                    </div>
                    <div class="af-field">
                        <textarea name="categories" id="categories" rows="6" class="form-textarea" placeholder="한 줄에 하나씩 입력"><?php echo $community_board_form_view['categories_value']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-header">
            <h2 class="card-title">첨부와 포인트</h2>
        </div>
        <div class="card-body">
            <div class="af-grid">
                <div class="af-row">
                    <div class="af-label">
                        <span class="form-label">첨부 제한</span>
                    </div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label for="upload_file_count" class="ui-form-inline-note">개수</label>
                            <input type="number" name="upload_file_count" value="<?php echo $community_board_form_view['upload_file_count_value']; ?>" id="upload_file_count" class="form-input af-input-sm" min="0">
                            <label for="upload_file_size" class="ui-form-inline-note">크기(byte)</label>
                            <input type="number" name="upload_file_size" value="<?php echo $community_board_form_view['upload_file_size_value']; ?>" id="upload_file_size" class="form-input af-input-sm" min="0">
                            <label for="upload_extensions" class="ui-form-inline-note">확장자</label>
                            <input type="text" name="upload_extensions" value="<?php echo $community_board_form_view['upload_extensions_value']; ?>" id="upload_extensions" class="form-input af-input-sm" placeholder="jpg|png|pdf">
                        </div>
                    </div>
                </div>

                <div class="af-row">
                    <div class="af-label">
                        <span class="form-label">포인트</span>
                    </div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label class="af-check form-label"><input type="checkbox" name="use_point" value="1"<?php echo $community_board_form_view['use_point_checked']; ?> class="form-checkbox"><span class="form-label">사용</span></label>
                            <label for="point_write" class="ui-form-inline-note">글</label>
                            <input type="number" name="point_write" value="<?php echo $community_board_form_view['point_write_value']; ?>" id="point_write" class="form-input af-input-sm">
                            <label for="point_comment" class="ui-form-inline-note">댓글</label>
                            <input type="number" name="point_comment" value="<?php echo $community_board_form_view['point_comment_value']; ?>" id="point_comment" class="form-input af-input-sm">
                            <label for="point_read" class="ui-form-inline-note">읽기</label>
                            <input type="number" name="point_read" value="<?php echo $community_board_form_view['point_read_value']; ?>" id="point_read" class="form-input af-input-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="admin-form-sticky-actions admin-form-actions admin-form-actions-split">
        <a href="<?php echo $community_board_form_view['list_url_attr']; ?>" class="btn btn-surface-default-soft">목록</a>
        <button type="submit" class="btn btn-solid-primary">저장</button>
    </div>
</form>
