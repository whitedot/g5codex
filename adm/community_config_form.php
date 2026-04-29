<?php
// 검증 지도: 커뮤니티 기본환경 설정 form controller다.
// 화면 배열은 community admin render 파일, 저장은 community_config_form_update.php에서 처리한다.
$sub_menu = "300050";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$community_config_form_view = community_admin_build_config_form_view();

admin_apply_page_view($community_config_form_view);
require_once './admin.head.php';
include_once G5_ADMIN_PATH . '/community_config_parts/form.php';
require_once './admin.tail.php';
