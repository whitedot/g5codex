<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<div class="member-summary">
    <div class="member-summary-stats">
        <span class="member-summary-meta">총 알림 <strong><?php echo $community_notification_view['total_count_text']; ?></strong></span>
    </div>
</div>

<form method="get" action="<?php echo $community_notification_view['search_action_attr']; ?>" class="member-search">
    <div class="member-search-fields">
        <label for="notification_status" class="member-field-label">상태</label>
        <select name="status" id="notification_status" class="form-select member-field-input">
            <?php foreach ($community_notification_view['status_options'] as $option) { ?>
                <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
            <?php } ?>
        </select>
        <label for="notification_stx" class="member-field-label">검색어</label>
        <input type="text" name="stx" value="<?php echo $community_notification_view['stx_value']; ?>" id="notification_stx" class="form-input member-field-input" placeholder="회원 ID, 이메일, 제목">
        <button type="submit" class="btn btn-solid-primary">검색</button>
    </div>
</form>

<div class="tbl_head01 tbl_wrap">
    <table>
        <caption>커뮤니티 알림 로그</caption>
        <thead>
        <tr>
            <th scope="col">번호</th>
            <th scope="col">이벤트</th>
            <th scope="col">대상</th>
            <th scope="col">수신자</th>
            <th scope="col">제목</th>
            <th scope="col">상태</th>
            <th scope="col">오류</th>
            <th scope="col">생성일</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($community_notification_view['items'] as $item) { ?>
            <tr>
                <td><?php echo $item['id_text']; ?></td>
                <td><?php echo $item['event_type_text']; ?></td>
                <td><?php echo $item['target_text']; ?></td>
                <td><?php echo $item['recipient_text']; ?></td>
                <td><?php echo $item['subject_text']; ?></td>
                <td><?php echo $item['status_text']; ?></td>
                <td><?php echo $item['error_text']; ?></td>
                <td><?php echo $item['created_at_text']; ?></td>
            </tr>
        <?php } ?>
        <?php if (empty($community_notification_view['items'])) { ?>
            <tr><td colspan="8" class="ui-table-empty"><?php echo $community_notification_view['empty_message']; ?></td></tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php echo $community_notification_view['paging_html']; ?>
