<section class="card admin-export-intro-card">
    <div class="card-header">
        <h2 class="card-title">회원 엑셀 생성</h2>
    </div>
    <div class="card-body admin-export-intro-body">
        <?php foreach ($member_export_view['intro_items'] as $item) { ?>
            <p><?php echo $item['html']; ?></p>
        <?php } ?>
    </div>
</section>

<div class="admin-export-total">
    <span class="admin-export-total-label">총건수</span>
    <?php if ($member_export_view['total_view']['has_error']) { ?>
    <span class="admin-export-total-value admin-export-total-error"><?php echo $member_export_view['total_view']['error_text']; ?></span>
    <?php } else { ?>
    <span class="admin-export-total-value"><?php echo $member_export_view['total_view']['count_text']; ?></span>
    <?php } ?>
</div>

<?php if (!$member_export_view['environment_ready']) { ?>
<div class="ui-alert ui-alert-danger admin-export-intro-card admin-export-runtime-alert">
    <strong>내보내기 실행 환경 확인 필요</strong>
    <p class="ui-alert-copy"><?php echo $member_export_view['environment_error_text']; ?></p>
</div>
<?php } ?>
