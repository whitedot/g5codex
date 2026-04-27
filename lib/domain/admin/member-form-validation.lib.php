<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function admin_validate_member_delete_request(array $request, array $member)
{
    $mb = $request['mb_id'] ? get_member($request['mb_id']) : array();

    if (!(isset($mb['mb_id']) && $mb['mb_id'])) {
        alert('회원자료가 존재하지 않습니다.');
    } elseif ($member['mb_id'] == $mb['mb_id']) {
        alert('로그인 중인 관리자는 삭제 할 수 없습니다.');
    } elseif (is_admin($mb['mb_id']) == 'super') {
        alert('최고 관리자는 삭제할 수 없습니다.');
    } elseif ($mb['mb_level'] >= $member['mb_level']) {
        alert('자신보다 권한이 높거나 같은 회원은 삭제할 수 없습니다.');
    }

    return $mb;
}

function admin_validate_member_delete_action()
{
    check_demo();
}

function admin_validate_member_form_update_action($w)
{
    if ($w == 'u') {
        check_demo();
    }
}
