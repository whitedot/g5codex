<?php // 회원 export 안내 partial이다. 안내 항목과 runtime 오류 문구는 export-view.lib.php에서 준비한다. ?>
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

<?php if (!$member_export_view['environment_ready']) { ?>
<div class="ui-alert ui-alert-danger admin-export-intro-card admin-export-runtime-alert">
    <strong>내보내기 실행 환경 확인 필요</strong>
    <p class="ui-alert-copy"><?php echo $member_export_view['environment_error_text']; ?></p>
</div>
<?php } ?>
