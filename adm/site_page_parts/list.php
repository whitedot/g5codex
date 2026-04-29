<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<div class="member-summary">
    <div class="member-summary-links">
        <a href="<?php echo $site_page_list_view['list_all_url_attr']; ?>" class="btn btn-surface-default-soft">전체 보기</a>
        <a href="<?php echo $site_page_list_view['add_url_attr']; ?>" class="btn btn-solid-primary">페이지 추가</a>
    </div>
    <div class="member-summary-stats">
        <span class="member-summary-meta">총 페이지 <strong><?php echo $site_page_list_view['total_count_text']; ?></strong></span>
    </div>
</div>

<div class="member-search-card">
    <form method="get" action="<?php echo $site_page_list_view['search_action_attr']; ?>">
        <div class="member-search-fields community-search-fields community-search-fields-wide">
            <div class="member-field">
                <label for="page_content_format" class="member-field-label">형식</label>
                <select name="content_format" id="page_content_format" class="form-select member-field-input">
                    <?php foreach ($site_page_list_view['content_format_options'] as $option) { ?>
                        <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="member-field">
                <label for="page_status" class="member-field-label">상태</label>
                <select name="status" id="page_status" class="form-select member-field-input">
                    <?php foreach ($site_page_list_view['status_options'] as $option) { ?>
                        <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="member-field">
                <label for="page_stx" class="member-field-label">검색어</label>
                <input type="text" name="stx" value="<?php echo $site_page_list_view['stx_value']; ?>" id="page_stx" class="form-input member-field-input" placeholder="제목, ID, 요약">
            </div>
            <button type="submit" class="btn btn-solid-primary member-search-submit">검색</button>
        </div>
    </form>
</div>

<div class="member-table-card community-table-card">
    <div class="table-wrapper">
        <table class="table community-list-table">
            <caption>페이지 목록</caption>
            <thead class="ui-table-head">
            <tr>
                <th scope="col">페이지</th>
                <th scope="col">주소</th>
                <th scope="col">형식</th>
                <th scope="col">상태</th>
                <th scope="col">기기</th>
                <th scope="col">수정일</th>
                <th scope="col" class="text-end">관리</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($site_page_list_view['items'] as $item) { ?>
                <tr>
                    <td>
                        <div class="community-primary">
                            <strong><?php echo $item['title_text']; ?></strong>
                            <span><?php echo $item['summary_text']; ?></span>
                        </div>
                    </td>
                    <td><a href="<?php echo $item['url_attr']; ?>" target="_blank"><?php echo $item['slug_text']; ?></a></td>
                    <td><?php echo $item['format_text']; ?></td>
                    <td><span class="community-status <?php echo $item['status_class']; ?>"><?php echo $item['status_text']; ?></span></td>
                    <td><?php echo $item['device_text']; ?></td>
                    <td class="community-date"><?php echo $item['updated_at_text']; ?></td>
                    <td class="text-end">
                        <div class="member-manage">
                            <a href="<?php echo $item['url_attr']; ?>" class="btn btn-sm btn-surface-default-soft" target="_blank">보기</a>
                            <a href="<?php echo $item['edit_url_attr']; ?>" class="btn btn-sm btn-surface-default-soft">수정</a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            <?php if (empty($site_page_list_view['items'])) { ?>
                <tr><td colspan="7" class="ui-table-empty"><?php echo $site_page_list_view['empty_message']; ?></td></tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo $site_page_list_view['paging_html']; ?>
