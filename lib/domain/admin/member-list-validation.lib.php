<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 목록 선택수정/선택삭제의 검증 규칙만 담당한다.
// 실제 DB 변경은 member-list-persist.lib.php, 처리 순서와 redirect는 member-list-update.lib.php를 확인한다.
// 이 파일에 SQL update나 화면 배열 조립을 추가하지 않는다.

function admin_validate_member_list_update_request(array $request)
{
    if (empty($request['chk'])) {
        alert($request['act_button'] . ' 하실 항목을 하나 이상 체크하세요.');
    }
}

function admin_member_list_update_error(array $mb, array $member, $is_admin)
{
    $display_mb_id = member_get_display_id($mb);

    if (!(isset($mb['mb_id']) && $mb['mb_id'])) {
        return $display_mb_id . ' : 회원자료가 존재하지 않습니다.\\n';
    }
    if (member_is_left($mb)) {
        return $display_mb_id . ' : 탈퇴 또는 삭제 처리된 회원은 수정할 수 없습니다.\\n';
    }
    if ($is_admin != 'super' && $mb['mb_level'] >= $member['mb_level']) {
        return $display_mb_id . ' : 자신보다 권한이 높거나 같은 회원은 수정할 수 없습니다.\\n';
    }
    if ($member['mb_id'] == $mb['mb_id']) {
        return $display_mb_id . ' : 로그인 중인 관리자는 수정 할 수 없습니다.\\n';
    }

    return '';
}

function admin_member_list_delete_error(array $mb, array $member, $is_admin)
{
    $display_mb_id = member_get_display_id($mb);

    if (!(isset($mb['mb_id']) && $mb['mb_id'])) {
        return $display_mb_id . ' : 회원자료가 존재하지 않습니다.\\n';
    }
    if ($member['mb_id'] == $mb['mb_id']) {
        return $display_mb_id . ' : 로그인 중인 관리자는 삭제 할 수 없습니다.\\n';
    }
    if (is_admin($mb['mb_id']) == 'super') {
        return $display_mb_id . ' : 최고 관리자는 삭제할 수 없습니다.\\n';
    }
    if ($is_admin != 'super' && $mb['mb_level'] >= $member['mb_level']) {
        return $display_mb_id . ' : 자신보다 권한이 높거나 같은 회원은 삭제할 수 없습니다.\\n';
    }

    return '';
}
