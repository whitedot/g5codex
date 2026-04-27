<?php
// 검증 지도: 이 controller는 회원 목록 화면의 실행 순서만 조립한다.
// 입력 정규화는 member-list-request.lib.php, 조회 SQL은 member-list-query.lib.php,
// 화면 배열은 member-list-view.lib.php, HTML 출력은 adm/member_list_parts/*.php를 확인한다.
$sub_menu = "200100";
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

$member_list_request = admin_read_member_list_request(g5_get_runtime_get_input(), $config);
$member_list_qstr = admin_bootstrap_build_qstr($member_list_request);
$member_list_view = admin_build_member_list_page_view($member_list_request, $member, $is_admin, $config, $member_list_qstr);

admin_apply_page_view($member_list_view);
require_once './admin.head.php';
include_once G5_ADMIN_PATH . '/member_list_parts/summary.php';
include_once G5_ADMIN_PATH . '/member_list_parts/search.php';
include_once G5_ADMIN_PATH . '/member_list_parts/table.php';
require_once './admin.tail.php';
