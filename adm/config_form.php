<?php
// 검증 지도: 관리자 기본환경 설정 화면 controller다.
// 설정 조회와 화면 배열은 config-view.lib.php, 저장 흐름은 config_form_update.php와 config-update.lib.php를 확인한다.
// 이 파일에는 POST 저장 로직이나 직접 SQL update를 추가하지 않는다.
$sub_menu = "100100";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

admin_require_super_admin($is_admin);

$config = admin_read_config_row();
$request_server = g5_get_runtime_server_input();
$config_form_view = admin_build_config_form_page_view($config, isset($request_server['REMOTE_ADDR']) ? $request_server['REMOTE_ADDR'] : '');

admin_apply_page_view($config_form_view);
$config_basic_view = $config_form_view['basic_view'];
$config_join_view = $config_form_view['join_view'];
$config_cert_view = $config_form_view['cert_view'];
$config_mail_view = $config_form_view['mail_view'];
require_once './admin.head.php';
?>

<?php echo admin_render_anchor_menu($config_form_view['pg_anchor_menu_view']); ?>

<form name="fconfigform" id="fconfigform" method="post" action="./config_form_update.php" class="admin-form-layout ui-form-theme ui-form-showcase" data-current-user-ip="<?php echo $config_form_view['current_user_ip']; ?>" data-webp-warning="<?php echo $config_form_view['webp_warning']; ?>">
    <input type="hidden" name="token" value="" id="token">

    <?php
    include_once G5_ADMIN_PATH . '/config_form_parts/basic.php';
    include_once G5_ADMIN_PATH . '/config_form_parts/join.php';
    include_once G5_ADMIN_PATH . '/config_form_parts/cert.php';
    include_once G5_ADMIN_PATH . '/config_form_parts/mail.php';
    ?>

    <div id="config_captcha_wrap" class="admin-captcha-panel" hidden>
        <h2 class="admin-captcha-title">캡차 입력</h2>
        <?php
        require_once G5_CAPTCHA_PATH . '/captcha.lib.php';
        $captcha_html = captcha_html();
        echo $captcha_html;
        ?>
    </div>

    <div class="admin-form-sticky-actions admin-form-actions admin-form-actions-primary">
        <button type="submit" class="btn btn-solid-primary" accesskey="s">저장</button>
    </div>
</form>

<?php
require_once './admin.tail.php';
?>
