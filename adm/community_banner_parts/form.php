<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<form method="post" action="<?php echo $community_banner_form_view['form_action_attr']; ?>" enctype="multipart/form-data" class="admin-form-layout ui-form-theme ui-form-showcase">
    <input type="hidden" name="token" value="<?php echo $community_banner_form_view['admin_token']; ?>">
    <input type="hidden" name="banner_id" value="<?php echo $community_banner_form_view['banner_id_value']; ?>">
    <input type="hidden" name="image_path" value="<?php echo $community_banner_form_view['image_path_value']; ?>">
    <input type="hidden" name="mobile_image_path" value="<?php echo $community_banner_form_view['mobile_image_path_value']; ?>">

    <section class="card">
        <div class="card-header">
            <h2 class="card-title">배너 설정</h2>
        </div>
        <div class="card-body">
            <div class="af-grid">
                <div class="af-row">
                    <div class="af-label"><label for="title" class="form-label">배너명</label></div>
                    <div class="af-field"><input type="text" name="title" value="<?php echo $community_banner_form_view['title_value']; ?>" id="title" class="form-input" required maxlength="255"></div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="position" class="form-label">위치</label></div>
                    <div class="af-field">
                        <select name="position" id="position" class="form-select">
                            <?php foreach ($community_banner_form_view['position_options'] as $option) { ?>
                                <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="image_file" class="form-label">PC 이미지</label></div>
                    <div class="af-field">
                        <input type="file" name="image_file" id="image_file" class="form-input" accept="image/*">
                        <?php if ($community_banner_form_view['image_url_attr'] !== '') { ?>
                            <p class="hint-text"><a href="<?php echo $community_banner_form_view['image_url_attr']; ?>" target="_blank">현재 이미지 보기</a> <label class="af-check form-label"><input type="checkbox" name="delete_image" value="1" class="form-checkbox"><span class="form-label">삭제</span></label></p>
                        <?php } ?>
                    </div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="mobile_image_file" class="form-label">모바일 이미지</label></div>
                    <div class="af-field">
                        <input type="file" name="mobile_image_file" id="mobile_image_file" class="form-input" accept="image/*">
                        <?php if ($community_banner_form_view['mobile_image_url_attr'] !== '') { ?>
                            <p class="hint-text"><a href="<?php echo $community_banner_form_view['mobile_image_url_attr']; ?>" target="_blank">현재 이미지 보기</a> <label class="af-check form-label"><input type="checkbox" name="delete_mobile_image" value="1" class="form-checkbox"><span class="form-label">삭제</span></label></p>
                        <?php } ?>
                    </div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="link_url" class="form-label">링크 URL</label></div>
                    <div class="af-field"><input type="text" name="link_url" value="<?php echo $community_banner_form_view['link_url_value']; ?>" id="link_url" class="form-input" maxlength="255"></div>
                </div>
                <div class="af-row">
                    <div class="af-label"><span class="form-label">노출 기간</span></div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label for="started_date" class="ui-form-inline-note">시작</label>
                            <input type="date" name="started_date" value="<?php echo $community_banner_form_view['started_date_value']; ?>" id="started_date" class="form-input af-input-sm">
                            <input type="time" name="started_time" value="<?php echo $community_banner_form_view['started_time_value']; ?>" id="started_time" class="form-input af-input-sm">
                            <label for="ended_date" class="ui-form-inline-note">종료</label>
                            <input type="date" name="ended_date" value="<?php echo $community_banner_form_view['ended_date_value']; ?>" id="ended_date" class="form-input af-input-sm">
                            <input type="time" name="ended_time" value="<?php echo $community_banner_form_view['ended_time_value']; ?>" id="ended_time" class="form-input af-input-sm">
                        </div>
                    </div>
                </div>
                <div class="af-row">
                    <div class="af-label"><span class="form-label">운영</span></div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label class="af-check form-label"><input type="checkbox" name="target_blank" value="1"<?php echo $community_banner_form_view['target_blank_checked']; ?> class="form-checkbox"><span class="form-label">새 창</span></label>
                            <label class="af-check form-label"><input type="checkbox" name="show_pc" value="1"<?php echo $community_banner_form_view['show_pc_checked']; ?> class="form-checkbox"><span class="form-label">PC</span></label>
                            <label class="af-check form-label"><input type="checkbox" name="show_mobile" value="1"<?php echo $community_banner_form_view['show_mobile_checked']; ?> class="form-checkbox"><span class="form-label">모바일</span></label>
                            <label for="list_order" class="ui-form-inline-note">정렬</label>
                            <input type="number" name="list_order" value="<?php echo $community_banner_form_view['list_order_value']; ?>" id="list_order" class="form-input af-input-sm">
                            <label for="status" class="ui-form-inline-note">상태</label>
                            <select name="status" id="status" class="form-select af-input-sm">
                                <?php foreach ($community_banner_form_view['status_options'] as $option) { ?>
                                    <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="admin-form-sticky-actions admin-form-actions admin-form-actions-split">
        <a href="<?php echo $community_banner_form_view['list_url_attr']; ?>" class="btn btn-surface-default-soft">목록</a>
        <button type="submit" class="btn btn-solid-primary">저장</button>
    </div>
</form>
