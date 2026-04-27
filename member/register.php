<?php
// 검증 지도: 회원가입 약관/동의 화면 controller다. 화면 배열은 render-page-view, 진행 상태 초기화는 flow-register-form에서 처리한다.
include_once('./_common.php');

member_validate_register_page_access($is_member);
member_reset_registration_progress();

$page_view = member_build_register_intro_page_view($config);
MemberPageController::renderPage('register', $page_view);
