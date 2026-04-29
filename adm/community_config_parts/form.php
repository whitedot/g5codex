<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<form method="post" action="<?php echo $community_config_form_view['form_action_attr']; ?>" class="admin-form-layout ui-form-theme ui-form-showcase">
    <input type="hidden" name="token" value="<?php echo $community_config_form_view['admin_token']; ?>">

    <section class="card">
        <div class="card-header">
            <h2 class="card-title">포인트 만료</h2>
        </div>
        <div class="card-body">
            <div class="af-grid">
                <div class="af-row">
                    <div class="af-label">
                        <label for="point_expire_days" class="form-label">만료 기준</label>
                    </div>
                    <div class="af-field">
                        <div class="af-inline">
                            <input type="number" name="point_expire_days" value="<?php echo $community_config_form_view['point_expire_days_value']; ?>" id="point_expire_days" class="form-input af-input-sm" min="0">
                            <span class="ui-form-inline-note">일</span>
                        </div>
                        <p class="hint-text"><?php echo $community_config_form_view['point_expire_rule_text']; ?> 0이면 만료일을 지정하지 않습니다.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-header">
            <h2 class="card-title">게시판 기본 권한</h2>
        </div>
        <div class="card-body">
            <div class="af-grid">
                <div class="af-row">
                    <div class="af-label">
                        <span class="form-label">권한 레벨</span>
                    </div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label for="board_read_level" class="ui-form-inline-note">읽기</label>
                            <select name="board_read_level" id="board_read_level" class="form-select af-input-sm">
                                <?php foreach ($community_config_form_view['board_read_level_options'] as $option) { ?>
                                    <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                                <?php } ?>
                            </select>
                            <label for="board_write_level" class="ui-form-inline-note">쓰기</label>
                            <select name="board_write_level" id="board_write_level" class="form-select af-input-sm">
                                <?php foreach ($community_config_form_view['board_write_level_options'] as $option) { ?>
                                    <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                                <?php } ?>
                            </select>
                            <label for="board_comment_level" class="ui-form-inline-note">댓글</label>
                            <select name="board_comment_level" id="board_comment_level" class="form-select af-input-sm">
                                <?php foreach ($community_config_form_view['board_comment_level_options'] as $option) { ?>
                                    <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="af-row">
                    <div class="af-label">
                        <span class="form-label">기본 기능</span>
                    </div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label class="af-check form-label"><input type="checkbox" name="board_use_category" value="1"<?php echo $community_config_form_view['board_use_category_checked']; ?> class="form-checkbox"><span class="form-label">카테고리</span></label>
                            <label class="af-check form-label"><input type="checkbox" name="board_use_latest" value="1"<?php echo $community_config_form_view['board_use_latest_checked']; ?> class="form-checkbox"><span class="form-label">최신글</span></label>
                            <label class="af-check form-label"><input type="checkbox" name="board_use_comment" value="1"<?php echo $community_config_form_view['board_use_comment_checked']; ?> class="form-checkbox"><span class="form-label">댓글</span></label>
                        </div>
                    </div>
                </div>

                <div class="af-row">
                    <div class="af-label">
                        <span class="form-label">메일 알림</span>
                    </div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label class="af-check form-label"><input type="checkbox" name="board_use_mail_post" value="1"<?php echo $community_config_form_view['board_use_mail_post_checked']; ?> class="form-checkbox"><span class="form-label">게시물 작성자</span></label>
                            <label class="af-check form-label"><input type="checkbox" name="board_use_mail_comment" value="1"<?php echo $community_config_form_view['board_use_mail_comment_checked']; ?> class="form-checkbox"><span class="form-label">댓글 작성자</span></label>
                            <label class="af-check form-label"><input type="checkbox" name="board_mail_admin" value="1"<?php echo $community_config_form_view['board_mail_admin_checked']; ?> class="form-checkbox"><span class="form-label">관리자</span></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-header">
            <h2 class="card-title">첨부와 포인트 기본값</h2>
        </div>
        <div class="card-body">
            <div class="af-grid">
                <div class="af-row">
                    <div class="af-label">
                        <span class="form-label">첨부 제한</span>
                    </div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label for="board_upload_file_count" class="ui-form-inline-note">개수</label>
                            <input type="number" name="board_upload_file_count" value="<?php echo $community_config_form_view['board_upload_file_count_value']; ?>" id="board_upload_file_count" class="form-input af-input-sm" min="0">
                            <label for="board_upload_file_size" class="ui-form-inline-note">크기(byte)</label>
                            <input type="number" name="board_upload_file_size" value="<?php echo $community_config_form_view['board_upload_file_size_value']; ?>" id="board_upload_file_size" class="form-input af-input-sm" min="0">
                            <label for="board_upload_extensions" class="ui-form-inline-note">확장자</label>
                            <input type="text" name="board_upload_extensions" value="<?php echo $community_config_form_view['board_upload_extensions_value']; ?>" id="board_upload_extensions" class="form-input af-input-sm" placeholder="jpg|png|pdf">
                        </div>
                    </div>
                </div>

                <div class="af-row">
                    <div class="af-label">
                        <span class="form-label">포인트</span>
                    </div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label class="af-check form-label"><input type="checkbox" name="board_use_point" value="1"<?php echo $community_config_form_view['board_use_point_checked']; ?> class="form-checkbox"><span class="form-label">사용</span></label>
                            <label for="board_point_write" class="ui-form-inline-note">글</label>
                            <input type="number" name="board_point_write" value="<?php echo $community_config_form_view['board_point_write_value']; ?>" id="board_point_write" class="form-input af-input-sm">
                            <label for="board_point_comment" class="ui-form-inline-note">댓글</label>
                            <input type="number" name="board_point_comment" value="<?php echo $community_config_form_view['board_point_comment_value']; ?>" id="board_point_comment" class="form-input af-input-sm">
                            <label for="board_point_read" class="ui-form-inline-note">읽기</label>
                            <input type="number" name="board_point_read" value="<?php echo $community_config_form_view['board_point_read_value']; ?>" id="board_point_read" class="form-input af-input-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="admin-form-sticky-actions admin-form-actions admin-form-actions-split">
        <a href="<?php echo $community_config_form_view['board_list_url_attr']; ?>" class="btn btn-surface-default-soft">게시판 관리</a>
        <button type="submit" class="btn btn-solid-primary">저장</button>
    </div>
</form>
