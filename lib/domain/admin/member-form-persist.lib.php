<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 관리자 회원 등록/수정의 DB 저장만 담당한다.
// action 권한/토큰 검증은 member-form-update.lib.php와 member-form-validation.lib.php에서 끝난 상태를 전제로 한다.
// 이 파일에 redirect/event/shell 상태 조립을 추가하지 않는다.

function admin_persist_member_form_request($w, array $request, array $member, $is_admin)
{
    $mb_id = $request['mb_id'];
    $mb_email = $request['mb_email'];
    $mb_nick = $request['mb_nick'];

    $existing_member = member_validate_admin_member_request($request, $member, $is_admin, $w);
    member_validate_admin_uniqueness($mb_id, $mb_nick, $mb_email, $w, $existing_member);

    if ($w == '') {
        $insert_params = member_build_admin_insert_fields($request);

        if (!member_insert_admin_account($insert_params)) {
            alert('회원정보를 저장하는 중 오류가 발생했습니다.');
        }

        return $mb_id;
    }

    if ($w == 'u') {
        $update_params = member_build_admin_update_fields($request, $existing_member);

        if (!member_update_admin_account($mb_id, $update_params)) {
            alert('회원정보를 수정하는 중 오류가 발생했습니다.');
        }

        return $mb_id;
    }

    alert('제대로 된 값이 넘어오지 않았습니다.');

    return '';
}
