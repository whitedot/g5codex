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

<div class="member-search-card">
    <form method="get" action="<?php echo $community_comment_view['search_action_attr']; ?>">
        <div class="member-search-fields community-search-fields community-search-fields-wide">
            <div class="member-field">
                <label for="comment_post_id" class="member-field-label">게시글</label>
                <input type="number" name="post_id" value="<?php echo $community_comment_view['post_id_value']; ?>" id="comment_post_id" class="form-input member-field-input" placeholder="게시글 번호">
            </div>
            <div class="member-field">
                <label for="comment_status" class="member-field-label">상태</label>
                <select name="status" id="comment_status" class="form-select member-field-input">
                    <?php foreach ($community_comment_view['status_options'] as $option) { ?>
                        <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="member-field">
                <label for="comment_stx" class="member-field-label">검색어</label>
                <input type="text" name="stx" value="<?php echo $community_comment_view['stx_value']; ?>" id="comment_stx" class="form-input member-field-input" placeholder="내용 또는 작성자">
            </div>
            <button type="submit" class="btn btn-solid-primary member-search-submit">검색</button>
        </div>
    </form>
</div>

<form method="post" action="<?php echo $community_comment_view['update_action_attr']; ?>" class="community-list-form">
    <input type="hidden" name="token" value="<?php echo $community_comment_view['admin_token']; ?>">
    <input type="hidden" name="return_query" value="<?php echo $community_comment_view['return_query_attr']; ?>">

    <div class="member-table-card community-table-card">
        <div class="table-wrapper">
            <table class="table community-list-table">
                <caption>커뮤니티 댓글 목록</caption>
                <thead class="ui-table-head">
                <tr>
                    <th scope="col"><input type="checkbox" onclick="var checked=this.checked; document.querySelectorAll('input[name=&quot;comment_id[]&quot;]').forEach(function(el){el.checked = checked;});"></th>
                    <th scope="col">댓글</th>
                    <th scope="col">게시글</th>
                    <th scope="col">작성자</th>
                    <th scope="col">상태</th>
                    <th scope="col">작성일</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($community_comment_view['items'] as $item) { ?>
                    <tr>
                        <td><input type="checkbox" name="comment_id[]" value="<?php echo $item['comment_id_attr']; ?>"></td>
                        <td>
                            <div class="community-primary">
                                <strong><?php echo $item['content_text']; ?></strong>
                                <span>#<?php echo $item['comment_id_text']; ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="community-primary community-primary-compact">
                                <a href="<?php echo $item['post_url_attr']; ?>" target="_blank">#<?php echo $item['post_id_text']; ?></a>
                                <span><?php echo $item['post_title_text']; ?></span>
                            </div>
                        </td>
                        <td><?php echo $item['author_text']; ?></td>
                        <td><span class="community-status <?php echo $item['status_class']; ?>"><?php echo $item['status_text']; ?></span></td>
                        <td class="community-date"><?php echo $item['created_at_text']; ?></td>
                    </tr>
                <?php } ?>
                <?php if (empty($community_comment_view['items'])) { ?>
                    <tr><td colspan="6" class="ui-table-empty"><?php echo $community_comment_view['empty_message']; ?></td></tr>
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
    </div>
</form>

<?php echo $community_comment_view['paging_html']; ?>
