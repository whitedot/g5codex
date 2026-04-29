<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<div class="member-summary">
    <div class="member-summary-stats">
        <span class="member-summary-meta">정상 <strong><?php echo $community_health_view['ok_count_text']; ?></strong></span>
        <span class="member-summary-meta">주의 <strong><?php echo $community_health_view['warning_count_text']; ?></strong></span>
        <span class="member-summary-meta">오류 <strong><?php echo $community_health_view['error_count_text']; ?></strong></span>
    </div>
</div>

<div class="member-table-card community-table-card">
    <div class="table-wrapper">
        <table class="table community-list-table">
            <caption>커뮤니티 운영 점검</caption>
            <thead class="ui-table-head">
            <tr>
                <th scope="col">항목</th>
                <th scope="col">상태</th>
                <th scope="col">내용</th>
                <th scope="col">조치</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($community_health_view['items'] as $item) { ?>
                <tr>
                    <td><strong class="community-id"><?php echo $item['label_text']; ?></strong></td>
                    <td><span class="community-status <?php echo $item['status_class']; ?>"><?php echo $item['status_text']; ?></span></td>
                    <td><?php echo $item['message_text']; ?></td>
                    <td><?php echo $item['action_text']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
