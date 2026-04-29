<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<form method="post" action="<?php echo $site_page_form_view['form_action_attr']; ?>" class="admin-form-layout ui-form-theme ui-form-showcase">
    <input type="hidden" name="token" value="<?php echo $site_page_form_view['admin_token']; ?>">
    <input type="hidden" name="page_id" value="<?php echo $site_page_form_view['page_id_value']; ?>">

    <section class="card">
        <div class="card-header">
            <h2 class="card-title">페이지 설정</h2>
        </div>
        <div class="card-body">
            <div class="af-grid">
                <div class="af-row">
                    <div class="af-label"><label for="slug" class="form-label">페이지 ID</label></div>
                    <div class="af-field">
                        <input type="text" name="slug" value="<?php echo $site_page_form_view['slug_value']; ?>" id="slug" class="form-input" required maxlength="100" placeholder="about-us">
                        <p class="hint-text">공개 주소는 /page.php?slug=페이지ID 형식으로 생성됩니다. 영문, 숫자, 하이픈, 밑줄을 사용할 수 있습니다.</p>
                    </div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="title" class="form-label">제목</label></div>
                    <div class="af-field"><input type="text" name="title" value="<?php echo $site_page_form_view['title_value']; ?>" id="title" class="form-input" required maxlength="255"></div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="summary" class="form-label">요약</label></div>
                    <div class="af-field"><input type="text" name="summary" value="<?php echo $site_page_form_view['summary_value']; ?>" id="summary" class="form-input" maxlength="255"></div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="content_format" class="form-label">본문 형식</label></div>
                    <div class="af-field">
                        <select name="content_format" id="content_format" class="form-select">
                            <?php foreach ($site_page_form_view['content_format_options'] as $option) { ?>
                                <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="af-row">
                    <div class="af-label"><label for="content" class="form-label">본문</label></div>
                    <div class="af-field">
                        <textarea name="content" id="content" class="form-input" rows="16" required><?php echo $site_page_form_view['content_value']; ?></textarea>
                    </div>
                </div>
                <div class="af-row">
                    <div class="af-label"><span class="form-label">노출</span></div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label class="af-check form-label"><input type="checkbox" name="show_pc" value="1"<?php echo $site_page_form_view['show_pc_checked']; ?> class="form-checkbox"><span class="form-label">PC</span></label>
                            <label class="af-check form-label"><input type="checkbox" name="show_mobile" value="1"<?php echo $site_page_form_view['show_mobile_checked']; ?> class="form-checkbox"><span class="form-label">모바일</span></label>
                        </div>
                    </div>
                </div>
                <div class="af-row">
                    <div class="af-label"><span class="form-label">운영</span></div>
                    <div class="af-field">
                        <div class="af-inline">
                            <label for="access_level" class="ui-form-inline-note">접근</label>
                            <select name="access_level" id="access_level" class="form-select af-input-sm">
                                <?php foreach ($site_page_form_view['access_level_options'] as $option) { ?>
                                    <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                                <?php } ?>
                            </select>
                            <label for="list_order" class="ui-form-inline-note">정렬</label>
                            <input type="number" name="list_order" value="<?php echo $site_page_form_view['list_order_value']; ?>" id="list_order" class="form-input af-input-sm">
                            <label for="status" class="ui-form-inline-note">상태</label>
                            <select name="status" id="status" class="form-select af-input-sm">
                                <?php foreach ($site_page_form_view['status_options'] as $option) { ?>
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
        <a href="<?php echo $site_page_form_view['list_url_attr']; ?>" class="btn btn-surface-default-soft">목록</a>
        <button type="submit" class="btn btn-solid-primary">저장</button>
    </div>
</form>
