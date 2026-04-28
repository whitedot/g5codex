<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 목록 선택수정/선택삭제의 업무 순서 파일이다.
// 요청 정규화는 member-list-request.lib.php, 행별 권한 검증은 member-list-validation.lib.php,
// update payload와 DB 저장은 member-list-persist.lib.php에서 담당한다.

function admin_process_member_list_update(array $request, array $member, $is_admin)
{
    $mb_datas = array();
    $msg = '';

    foreach ($request['chk'] as $selected_index) {
        $k = (int) $selected_index;
        $mb_id = isset($request['mb_id'][$k]) ? $request['mb_id'][$k] : '';
        $mb_datas[] = $mb = get_member($mb_id);
        $error = admin_member_list_update_error($mb, $member, $is_admin);
        if ($error !== '') {
            $msg .= $error;
            continue;
        }

        admin_apply_member_list_update(admin_build_member_list_update_payload($request, $k, $mb));
    }

    return array('mb_datas' => $mb_datas, 'msg' => $msg);
}

function admin_process_member_list_delete(array $request, array $member, $is_admin)
{
    $mb_datas = array();
    $msg = '';

    foreach ($request['chk'] as $selected_index) {
        $k = (int) $selected_index;
        $mb_id = isset($request['mb_id'][$k]) ? $request['mb_id'][$k] : '';
        $mb_datas[] = $mb = get_member($mb_id);
        $error = admin_member_list_delete_error($mb, $member, $is_admin);
        if ($error !== '') {
            $msg .= $error;
            continue;
        }

        member_delete($mb['mb_id']);
    }

    return array('mb_datas' => $mb_datas, 'msg' => $msg);
}

function admin_complete_member_list_update_request(array $request, array $member, $is_admin, $auth, $sub_menu, $qstr)
{
    admin_validate_member_list_update_request($request);

    auth_check_menu($auth, $sub_menu, 'w');
    check_admin_token();

    if ($request['act_button'] == '선택수정') {
        $result = admin_process_member_list_update($request, $member, $is_admin);
    } elseif ($request['act_button'] == '선택삭제') {
        $result = admin_process_member_list_delete($request, $member, $is_admin);
    } else {
        alert('제대로 된 값이 넘어오지 않았습니다.');
    }

    if ($result['msg']) {
        alert($result['msg']);
    }

    run_event('admin_member_list_update', $request['act_button'], $result['mb_datas']);
    if ($request['act_button'] === '선택수정') {
        admin_set_flash_message('success', '저장완료');
    } elseif ($request['act_button'] === '선택삭제') {
        admin_set_flash_message('success', '삭제 완료');
    }
    goto_url('./member_list.php?' . $qstr);
}
