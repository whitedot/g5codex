<?php
// 검증 지도: 비밀번호 찾기 화면 controller다. 접근 검증은 validation-auth, 화면 배열은 render-page-view에서 처리한다.
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

member_validate_password_lost_page_access($is_member);

$page_view = member_build_password_lost_page_view($config);
MemberPageController::renderPage('password_lost', $page_view);
