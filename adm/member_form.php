<?php
// 검증 지도: 이 controller는 관리자 회원 등록/수정 화면만 렌더링한다.
// 입력 정규화는 member-form-request.lib.php, 화면 배열은 member-form-view.lib.php,
// 저장 흐름은 member_form_update.php와 member-form-update.lib.php를 확인한다.
$sub_menu = "200100";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$admin_get_input = g5_get_runtime_get_input();
$member_list_request = admin_read_member_list_request($admin_get_input, $config);
$member_form_request = admin_read_member_form_request($admin_get_input);
$member_form_view = admin_build_member_form_view($member_form_request, $member, $is_admin, $config);
$page_view = admin_build_member_form_page_view($member_form_view, $config, $member_list_request);
$basic_view = $page_view['sections']['basic'];
$contact_view = $page_view['sections']['contact'];
$consent_view = $page_view['sections']['consent'];
$profile_view = $page_view['sections']['profile'];
$history_view = $page_view['sections']['history'];

admin_apply_page_view($page_view);
require_once './admin.head.php';
?>

<div data-admin-member-form>
<?php echo admin_render_anchor_menu($page_view['pg_anchor_menu_view']); ?>

<form name="fmember" id="fmember" action="./member_form_update.php" method="post" class="admin-form-layout ui-form-theme ui-form-showcase" autocomplete="off">
    <?php foreach ($page_view['hidden_fields'] as $hidden_field) { ?>
        <input type="hidden" name="<?php echo $hidden_field['name_attr']; ?>" value="<?php echo $hidden_field['value_attr']; ?>">
    <?php } ?>
    <input type="hidden" name="token" value="<?php echo $page_view['admin_token']; ?>" id="token">
    <div class="sr-only" aria-hidden="true">
        <label for="member_form_fake_username">자동완성 방지 아이디</label>
        <input type="text" id="member_form_fake_username" name="member_form_fake_username" tabindex="-1" autocomplete="username">
        <label for="member_form_fake_password">자동완성 방지 비밀번호</label>
        <input type="password" id="member_form_fake_password" name="member_form_fake_password" tabindex="-1" autocomplete="current-password">
    </div>

    <?php // 기본 정보
    include_once G5_ADMIN_PATH.'/member_form_parts/basic.php';

    // 연락처 및 주소
    include_once G5_ADMIN_PATH.'/member_form_parts/contact.php';

    // 수신 및 공개 설정
    include_once G5_ADMIN_PATH.'/member_form_parts/consent.php';

    // 프로필 및 메모
    include_once G5_ADMIN_PATH.'/member_form_parts/profile.php';

    // 인증 및 활동 내역
    include_once G5_ADMIN_PATH.'/member_form_parts/history.php';
    ?>

    <div class="admin-form-sticky-actions admin-form-actions admin-form-actions-split">
        <a href="<?php echo $page_view['list_url']; ?>" class="btn btn-surface-default-soft">목록</a>
        <button type="submit" class="btn btn-solid-primary" accesskey="s">저장</button>
    </div>
</form>
</div>

<?php

run_event('admin_member_form_after', $page_view['event_member'], $page_view['event_mode']);

require_once './admin.tail.php';
