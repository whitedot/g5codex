<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<div class="member-summary">
    <div class="member-summary-links">
        <a href="<?php echo $community_menu_list_view['list_all_url_attr']; ?>" class="btn btn-surface-default-soft">전체 보기</a>
        <a href="<?php echo $community_menu_list_view['add_url_attr']; ?>" class="btn btn-solid-primary">메뉴 추가</a>
    </div>
    <div class="member-summary-stats">
        <span class="member-summary-meta">총 메뉴 <strong><?php echo $community_menu_list_view['total_count_text']; ?></strong></span>
    </div>
</div>

<div class="member-search-card">
    <form method="get" action="<?php echo $community_menu_list_view['search_action_attr']; ?>">
        <div class="member-search-fields community-search-fields">
            <div class="member-field">
                <label for="menu_status" class="member-field-label">상태</label>
                <select name="status" id="menu_status" class="form-select member-field-input">
                    <?php foreach ($community_menu_list_view['status_options'] as $option) { ?>
                        <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="member-field">
                <label for="menu_stx" class="member-field-label">검색어</label>
                <input type="text" name="stx" value="<?php echo $community_menu_list_view['stx_value']; ?>" id="menu_stx" class="form-input member-field-input" placeholder="메뉴명, 대상, URL">
            </div>
            <button type="submit" class="btn btn-solid-primary member-search-submit">검색</button>
        </div>
    </form>
</div>

<div class="member-table-card community-table-card">
    <div class="table-wrapper">
        <table class="table community-list-table">
            <caption>사이트 메뉴 목록</caption>
            <thead class="ui-table-head">
            <tr>
                <th scope="col">메뉴</th>
                <th scope="col">상위</th>
                <th scope="col">유형</th>
                <th scope="col">대상</th>
                <th scope="col">상태</th>
                <th scope="col">기기</th>
                <th scope="col" class="text-end">관리</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($community_menu_list_view['items'] as $item) { ?>
                <tr>
                    <td>
                        <div class="community-primary">
                            <strong><?php echo $item['name_text']; ?></strong>
                            <span>#<?php echo $item['menu_id_text']; ?></span>
                        </div>
                    </td>
                    <td><?php echo $item['parent_text']; ?></td>
                    <td><?php echo $item['type_text']; ?></td>
                    <td><a href="<?php echo $item['url_attr']; ?>" target="_blank" rel="noopener"><?php echo $item['target_text']; ?></a></td>
                    <td><span class="community-status <?php echo $item['status_class']; ?>"><?php echo $item['status_text']; ?></span></td>
                    <td><?php echo $item['device_text']; ?></td>
                    <td class="text-end">
                        <div class="member-manage">
                            <a href="<?php echo $item['edit_url_attr']; ?>" class="btn btn-sm btn-surface-default-soft">수정</a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            <?php if (empty($community_menu_list_view['items'])) { ?>
                <tr><td colspan="7" class="ui-table-empty"><?php echo $community_menu_list_view['empty_message']; ?></td></tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo $community_menu_list_view['paging_html']; ?>
