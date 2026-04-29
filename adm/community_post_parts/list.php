<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<div class="member-summary">
    <div class="member-summary-stats">
        <span class="member-summary-meta">총 게시글 <strong><?php echo $community_post_view['total_count_text']; ?></strong></span>
    </div>
</div>

<div class="member-search-card">
    <form method="get" action="<?php echo $community_post_view['search_action_attr']; ?>">
        <div class="member-search-fields community-search-fields community-search-fields-wide">
            <div class="member-field">
                <label for="post_board_id" class="member-field-label">게시판</label>
                <input type="text" name="board_id" value="<?php echo $community_post_view['board_id_value']; ?>" id="post_board_id" class="form-input member-field-input" placeholder="게시판 ID">
            </div>
            <div class="member-field">
                <label for="post_status" class="member-field-label">상태</label>
                <select name="status" id="post_status" class="form-select member-field-input">
                    <?php foreach ($community_post_view['status_options'] as $option) { ?>
                        <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="member-field">
                <label for="post_stx" class="member-field-label">검색어</label>
                <input type="text" name="stx" value="<?php echo $community_post_view['stx_value']; ?>" id="post_stx" class="form-input member-field-input" placeholder="제목 또는 작성자">
            </div>
            <button type="submit" class="btn btn-solid-primary member-search-submit">검색</button>
        </div>
    </form>
</div>

<form method="post" action="<?php echo $community_post_view['update_action_attr']; ?>" class="community-list-form">
    <input type="hidden" name="token" value="<?php echo $community_post_view['admin_token']; ?>">
    <input type="hidden" name="return_query" value="<?php echo $community_post_view['return_query_attr']; ?>">

    <div class="member-table-card community-table-card">
        <div class="table-wrapper">
            <table class="table community-list-table">
                <caption>커뮤니티 게시글 목록</caption>
                <thead class="ui-table-head">
                <tr>
                    <th scope="col"><input type="checkbox" onclick="var checked=this.checked; document.querySelectorAll('input[name=&quot;post_id[]&quot;]').forEach(function(el){el.checked = checked;});"></th>
                    <th scope="col">게시글</th>
                    <th scope="col">게시판</th>
                    <th scope="col">작성자</th>
                    <th scope="col">상태</th>
                    <th scope="col">반응</th>
                    <th scope="col">작성일</th>
                    <th scope="col" class="text-end">관리</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($community_post_view['items'] as $item) { ?>
                    <tr>
                        <td><input type="checkbox" name="post_id[]" value="<?php echo $item['post_id_attr']; ?>"></td>
                        <td>
                            <div class="community-primary">
                                <strong><?php echo $item['title_text']; ?></strong>
                                <span>#<?php echo $item['post_id_text']; ?><?php echo $item['notice_text'] !== '' ? ' · ' . $item['notice_text'] : ''; ?></span>
                            </div>
                        </td>
                        <td><?php echo $item['board_id_text']; ?></td>
                        <td><?php echo $item['author_text']; ?></td>
                        <td><span class="community-status <?php echo $item['status_class']; ?>"><?php echo $item['status_text']; ?></span></td>
                        <td>
                            <div class="community-meta-list">
                                <span>댓글 <?php echo $item['comment_count_text']; ?></span>
                                <span>첨부 <?php echo $item['attachment_count_text']; ?></span>
                            </div>
                        </td>
                        <td class="community-date"><?php echo $item['created_at_text']; ?></td>
                        <td class="text-end">
                            <div class="member-manage">
                                <a href="<?php echo $item['view_url_attr']; ?>" class="btn btn-sm btn-surface-default-soft" target="_blank">보기</a>
                                <a href="<?php echo $item['comment_url_attr']; ?>" class="btn btn-sm btn-surface-default-soft">댓글</a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php if (empty($community_post_view['items'])) { ?>
                    <tr><td colspan="8" class="ui-table-empty"><?php echo $community_post_view['empty_message']; ?></td></tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="member-list-actions community-bulk-actions">
            <label for="community_post_bulk_action" class="sr-only">게시글 선택 작업</label>
            <select name="action" id="community_post_bulk_action" class="form-select community-action-select">
                <option value="">선택 작업</option>
                <option value="publish">공개</option>
                <option value="hide">숨김</option>
                <option value="delete">삭제</option>
                <option value="notice_on">공지 지정</option>
                <option value="notice_off">공지 해제</option>
            </select>
            <button type="submit" class="btn btn-solid-primary community-action-submit">적용</button>
        </div>
    </div>
</form>

<?php echo $community_post_view['paging_html']; ?>
