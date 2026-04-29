<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<form method="post" action="<?php echo $community_group_form_view['form_action_attr']; ?>" class="admin-form-layout ui-form-theme ui-form-showcase">
    <input type="hidden" name="token" value="<?php echo $community_group_form_view['admin_token']; ?>">
    <input type="hidden" name="original_group_id" value="<?php echo $community_group_form_view['original_group_id_attr']; ?>">

    <section class="card">
        <div class="card-header">
            <h2 class="card-title">기본 설정</h2>
        </div>
        <div class="card-body">
            <div class="af-grid">
                <div class="af-row">
                    <div class="af-label"><label for="group_id" class="form-label">그룹 ID</label></div>
                    <div class="af-field"><input type="text" name="group_id" value="<?php echo $community_group_form_view['group_id_value']; ?>" id="group_id" class="form-input" required maxlength="50"<?php echo $community_group_form_view['group_id_readonly_attr']; ?>></div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="name" class="form-label">그룹 이름</label></div>
                    <div class="af-field"><input type="text" name="name" value="<?php echo $community_group_form_view['name_value']; ?>" id="name" class="form-input" required maxlength="255"></div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="description" class="form-label">설명</label></div>
                    <div class="af-field"><textarea name="description" id="description" rows="4" class="form-textarea"><?php echo $community_group_form_view['description_value']; ?></textarea></div>
                </div>
                <div class="af-row">
                    <div class="af-label"><span class="form-label">기본 권한</span></div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label for="read_level" class="ui-form-inline-note">읽기</label>
                            <select name="read_level" id="read_level" class="form-select af-input-sm">
                                <?php foreach ($community_group_form_view['read_level_options'] as $option) { ?>
                                    <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                                <?php } ?>
                            </select>
                            <label for="write_level" class="ui-form-inline-note">쓰기</label>
                            <select name="write_level" id="write_level" class="form-select af-input-sm">
                                <?php foreach ($community_group_form_view['write_level_options'] as $option) { ?>
                                    <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                                <?php } ?>
                            </select>
                            <label for="comment_level" class="ui-form-inline-note">댓글</label>
                            <select name="comment_level" id="comment_level" class="form-select af-input-sm">
                                <?php foreach ($community_group_form_view['comment_level_options'] as $option) { ?>
                                    <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="list_order" class="form-label">정렬</label></div>
                    <div class="af-field"><input type="number" name="list_order" value="<?php echo $community_group_form_view['list_order_value']; ?>" id="list_order" class="form-input af-input-sm"></div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="status" class="form-label">상태</label></div>
                    <div class="af-field">
                        <select name="status" id="status" class="form-select">
                            <?php foreach ($community_group_form_view['status_options'] as $option) { ?>
                                <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="admin-form-sticky-actions admin-form-actions admin-form-actions-split">
        <a href="<?php echo $community_group_form_view['list_url_attr']; ?>" class="btn btn-surface-default-soft">목록</a>
        <button type="submit" class="btn btn-solid-primary">저장</button>
    </div>
</form>
