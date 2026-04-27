<?php // 회원 목록 상단 요약 partial이다. 값 계산은 member-list-view.lib.php에서 끝난 상태를 전제로 한다. ?>
<div class="member-summary">
    <div class="member-summary-links">
        <a href="<?php echo $member_list_view['list_all_url_attr']; ?>" class="btn btn-surface-default-soft">전체 보기</a>
    </div>

    <div class="member-summary-stats">
        <span class="member-summary-meta">총회원 <strong><?php echo $member_list_view['total_count_text']; ?></strong></span>
        <?php foreach ($member_list_view['summary_filter_links'] as $summary_filter_link) { ?>
            <a href="<?php echo $summary_filter_link['href_attr']; ?>" class="member-summary-meta"<?php echo $summary_filter_link['aria_current_attr']; ?>><?php echo $summary_filter_link['label_text']; ?> <?php echo $summary_filter_link['count_text']; ?></a>
        <?php } ?>
    </div>
</div>

<div class="member-notice">
    <span class="member-notice-icon" aria-hidden="true">i</span>
    <div class="member-notice-copy">
        <strong><?php echo $member_list_view['notice_title']; ?></strong>
        <p><?php echo $member_list_view['notice_body']; ?></p>
    </div>
</div>
