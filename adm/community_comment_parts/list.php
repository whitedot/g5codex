<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<div class="member-summary">
    <div class="member-summary-stats">
        <span class="member-summary-meta">총 댓글 <strong><?php echo $community_comment_view['total_count_text']; ?></strong></span>
    </div>
</div>

<form method="get" action="<?php echo $community_comment_view['search_action_attr']; ?>" class="member-search">
    <div class="member-search-fields">
        <label for="comment_post_id" class="member-field-label">게시글</label>
        <input type="number" name="post_id" value="<?php echo $community_comment_view['post_id_value']; ?>" id="comment_post_id" class="form-input member-field-input" placeholder="게시글 번호">
        <label for="comment_status" class="member-field-label">상태</label>
        <select name="status" id="comment_status" class="form-select member-field-input">
            <?php foreach ($community_comment_view['status_options'] as $option) { ?>
                <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
            <?php } ?>
        </select>
        <label for="comment_stx" class="member-field-label">검색어</label>
        <input type="text" name="stx" value="<?php echo $community_comment_view['stx_value']; ?>" id="comment_stx" class="form-input member-field-input" placeholder="내용 또는 작성자">
        <button type="submit" class="btn btn-solid-primary">검색</button>
    </div>
</form>

<form method="post" action="<?php echo $community_comment_view['update_action_attr']; ?>">
    <input type="hidden" name="token" value="<?php echo $community_comment_view['admin_token']; ?>">
    <input type="hidden" name="return_query" value="<?php echo $community_comment_view['return_query_attr']; ?>">

    <div class="tbl_head01 tbl_wrap">
        <table>
            <caption>커뮤니티 댓글 목록</caption>
            <thead>
            <tr>
                <th scope="col"><input type="checkbox" onclick="var checked=this.checked; document.querySelectorAll('input[name=&quot;comment_id[]&quot;]').forEach(function(el){el.checked = checked;});"></th>
                <th scope="col">번호</th>
                <th scope="col">게시글</th>
                <th scope="col">내용</th>
                <th scope="col">작성자</th>
                <th scope="col">상태</th>
                <th scope="col">작성일</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($community_comment_view['items'] as $item) { ?>
                <tr>
                    <td><input type="checkbox" name="comment_id[]" value="<?php echo $item['comment_id_attr']; ?>"></td>
                    <td><?php echo $item['comment_id_text']; ?></td>
                    <td><?php echo $item['post_id_text']; ?></td>
                    <td><?php echo $item['content_text']; ?></td>
                    <td><?php echo $item['author_text']; ?></td>
                    <td><?php echo $item['status_text']; ?></td>
                    <td><?php echo $item['created_at_text']; ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($community_comment_view['items'])) { ?>
                <tr><td colspan="7" class="ui-table-empty"><?php echo $community_comment_view['empty_message']; ?></td></tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="member-list-actions">
        <select name="action" class="form-select">
            <option value="">선택 작업</option>
            <option value="publish">공개</option>
            <option value="hide">숨김</option>
            <option value="delete">삭제</option>
        </select>
        <button type="submit" class="btn btn-solid-primary">적용</button>
    </div>
</form>

<?php echo $community_comment_view['paging_html']; ?>
