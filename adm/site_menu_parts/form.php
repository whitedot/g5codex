<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<form method="post" action="<?php echo $community_menu_form_view['form_action_attr']; ?>" class="admin-form-layout ui-form-theme ui-form-showcase">
    <input type="hidden" name="token" value="<?php echo $community_menu_form_view['admin_token']; ?>">
    <input type="hidden" name="menu_id" value="<?php echo $community_menu_form_view['menu_id_value']; ?>">

    <section class="card">
        <div class="card-header">
            <h2 class="card-title">메뉴 설정</h2>
        </div>
        <div class="card-body">
            <div class="af-grid">
                <div class="af-row">
                    <div class="af-label"><label for="name" class="form-label">메뉴명</label></div>
                    <div class="af-field"><input type="text" name="name" value="<?php echo $community_menu_form_view['name_value']; ?>" id="name" class="form-input" required maxlength="255"></div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="parent_id" class="form-label">상위 메뉴</label></div>
                    <div class="af-field">
                        <select name="parent_id" id="parent_id" class="form-select">
                            <?php foreach ($community_menu_form_view['parent_options'] as $option) { ?>
                                <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="menu_type" class="form-label">메뉴 유형</label></div>
                    <div class="af-field">
                        <select name="menu_type" id="menu_type" class="form-select">
                            <?php foreach ($community_menu_form_view['menu_type_options'] as $option) { ?>
                                <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="target_id" class="form-label">연결 대상 ID</label></div>
                    <div class="af-field">
                        <input type="text" name="target_id" value="<?php echo $community_menu_form_view['target_id_value']; ?>" id="target_id" class="form-input" maxlength="100" placeholder="페이지 ID, 게시판 그룹 ID 또는 게시판 ID">
                    </div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="url" class="form-label">직접 URL</label></div>
                    <div class="af-field"><input type="text" name="url" value="<?php echo $community_menu_form_view['url_value']; ?>" id="url" class="form-input" maxlength="255" placeholder="https:// 또는 /path"></div>
                </div>
                <div class="af-row">
                    <div class="af-label"><span class="form-label">노출</span></div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label class="af-check form-label"><input type="checkbox" name="target_blank" value="1"<?php echo $community_menu_form_view['target_blank_checked']; ?> class="form-checkbox"><span class="form-label">새 창</span></label>
                            <label class="af-check form-label"><input type="checkbox" name="show_pc" value="1"<?php echo $community_menu_form_view['show_pc_checked']; ?> class="form-checkbox"><span class="form-label">PC</span></label>
                            <label class="af-check form-label"><input type="checkbox" name="show_mobile" value="1"<?php echo $community_menu_form_view['show_mobile_checked']; ?> class="form-checkbox"><span class="form-label">모바일</span></label>
                        </div>
                    </div>
                </div>
                <div class="af-row">
                    <div class="af-label"><span class="form-label">운영</span></div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label for="access_level" class="ui-form-inline-note">접근</label>
                            <select name="access_level" id="access_level" class="form-select af-input-sm">
                                <?php foreach ($community_menu_form_view['access_level_options'] as $option) { ?>
                                    <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                                <?php } ?>
                            </select>
                            <label for="list_order" class="ui-form-inline-note">정렬</label>
                            <input type="number" name="list_order" value="<?php echo $community_menu_form_view['list_order_value']; ?>" id="list_order" class="form-input af-input-sm">
                            <label for="status" class="ui-form-inline-note">상태</label>
                            <select name="status" id="status" class="form-select af-input-sm">
                                <?php foreach ($community_menu_form_view['status_options'] as $option) { ?>
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
        <a href="<?php echo $community_menu_form_view['list_url_attr']; ?>" class="btn btn-surface-default-soft">목록</a>
        <button type="submit" class="btn btn-solid-primary">저장</button>
    </div>
</form>
