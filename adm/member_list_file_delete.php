<?php
// 검증 지도: 관리자 회원 export 임시 파일 정리 화면 controller다.
// 정리 실행과 결과 view는 export-maintenance.lib.php에서 담당한다.
// 이 파일에는 파일 삭제 조건이나 경로 계산 로직을 직접 추가하지 않는다.
$sub_menu = '200400';
require_once './_common.php';

$page_view = admin_complete_member_list_file_delete_request($is_admin);
$delete_result_view = $page_view['result'];
admin_apply_page_view($page_view);
require_once G5_ADMIN_PATH . '/admin.head.php';
?>

<section class="card admin-file-delete-card">
    <p class="admin-file-delete-copy">
        완료 메시지가 나오기 전에는 프로그램 실행을 중지하지 마십시오.
    </p>

    <ul class="admin-file-delete-list">
        <?php foreach ($delete_result_view['messages'] as $message) { ?>
            <li><?php echo $message; ?></li>
        <?php } ?>
    </ul>

    <p class="admin-file-delete-result">
        <strong>회원관리파일 <?php echo $delete_result_view['count']; ?>건 삭제 완료됐습니다.</strong><br>
        프로그램의 실행을 끝마치셔도 좋습니다.
    </p>
</section>

<?php
require_once G5_ADMIN_PATH . '/admin.tail.php';
