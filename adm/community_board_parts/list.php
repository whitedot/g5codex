<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<div class="member-summary">
    <div class="member-summary-links">
        <a href="<?php echo $community_board_list_view['list_all_url_attr']; ?>" class="btn btn-surface-default-soft">전체 보기</a>
        <a href="<?php echo $community_board_list_view['add_url_attr']; ?>" class="btn btn-solid-primary">게시판 추가</a>
    </div>
    <div class="member-summary-stats">
        <span class="member-summary-meta">총 게시판 <strong><?php echo $community_board_list_view['total_count_text']; ?></strong></span>
    </div>
</div>

<form method="get" action="<?php echo $community_board_list_view['search_action_attr']; ?>" class="member-search">
    <div class="member-search-fields">
        <label for="community_status" class="member-field-label">상태</label>
        <select name="status" id="community_status" class="form-select member-field-input">
            <?php foreach ($community_board_list_view['status_options'] as $option) { ?>
                <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
            <?php } ?>
        </select>
        <label for="community_stx" class="member-field-label">검색어</label>
        <input type="text" name="stx" value="<?php echo $community_board_list_view['stx_value']; ?>" id="community_stx" class="form-input member-field-input" placeholder="게시판 ID 또는 이름">
        <button type="submit" class="btn btn-solid-primary">검색</button>
    </div>
</form>

<div class="tbl_head01 tbl_wrap">
    <table>
        <caption>커뮤니티 게시판 목록</caption>
        <thead>
        <tr>
            <th scope="col">게시판 ID</th>
            <th scope="col">이름</th>
            <th scope="col">상태</th>
            <th scope="col">권한</th>
            <th scope="col">카테고리</th>
            <th scope="col">댓글</th>
            <th scope="col">최신글</th>
            <th scope="col">관리</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($community_board_list_view['items'] as $item) { ?>
            <tr>
                <td><?php echo $item['board_id_text']; ?></td>
                <td><?php echo $item['name_text']; ?></td>
                <td><?php echo $item['status_text']; ?></td>
                <td>읽기 <?php echo $item['read_level_text']; ?> / 쓰기 <?php echo $item['write_level_text']; ?> / 댓글 <?php echo $item['comment_level_text']; ?></td>
                <td><?php echo $item['use_category_text']; ?></td>
                <td><?php echo $item['use_comment_text']; ?></td>
                <td><?php echo $item['use_latest_text']; ?></td>
                <td><a href="<?php echo $item['edit_url_attr']; ?>" class="btn btn-sm btn-surface-default-soft">수정</a></td>
            </tr>
        <?php } ?>
        <?php if (empty($community_board_list_view['items'])) { ?>
            <tr><td colspan="8" class="ui-table-empty"><?php echo $community_board_list_view['empty_message']; ?></td></tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php echo $community_board_list_view['paging_html']; ?>
