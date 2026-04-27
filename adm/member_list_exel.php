<?php
// 검증 지도: 이 controller는 회원 export 화면을 렌더링한다.
// runtime 준비 상태는 export-runtime.lib.php, 필터와 총건수 화면 배열은 export-view.lib.php,
// 실제 다운로드/SSE 흐름은 member_list_exel_export.php와 export-stream.lib.php를 확인한다.
$sub_menu = "200400";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$page_request = admin_build_member_export_page_request(g5_get_runtime_get_input(), $config, $g5, isset($member) && is_array($member) ? $member : array());
$member_export_view = $page_request['view'];
$member_export_filter_view = $member_export_view['filter_view'];

admin_apply_page_view($member_export_view);
require_once './admin.head.php';
?>
<div
    data-admin-member-export
    data-environment-ready="<?php echo $member_export_view['environment_ready_attr']; ?>"
    data-environment-error="<?php echo $member_export_view['environment_error_attr']; ?>"
    <?php foreach ($member_export_view['client_config_attrs'] as $name => $value) { ?>
        data-<?php echo $name; ?>="<?php echo $value; ?>"
    <?php } ?>
>
    <?php include_once G5_ADMIN_PATH . '/member_list_exel_parts/intro.php'; ?>
    <?php include_once G5_ADMIN_PATH . '/member_list_exel_parts/filter.php'; ?>
</div>

<?php
require_once './admin.tail.php';
