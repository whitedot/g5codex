<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 관리자 회원 삭제/저장 action의 업무 순서 파일이다.
// 요청 정규화는 member-form-request.lib.php, action 검증은 member-form-validation.lib.php,
// 실제 DB 저장은 member-form-persist.lib.php에서 담당한다.

function admin_build_member_delete_redirect($qstr)
{
    return "./member_list.php?{$qstr}";
}

function admin_complete_member_delete_request(array $delete_action_request, array $member, $auth, $sub_menu)
{
    admin_validate_member_delete_action();

    auth_check_menu($auth, $sub_menu, 'd');

    $request = $delete_action_request['delete'];
    $mb = admin_validate_member_delete_request($request, $member);
    check_admin_token();

    member_delete($mb['mb_id']);

    goto_url(admin_build_member_delete_redirect($delete_action_request['list_qstr']));
}

function admin_build_member_form_update_redirect($qstr, $mb_id)
{
    return './member_form.php?' . $qstr . '&amp;w=u&amp;mb_id=' . $mb_id;
}

if (!function_exists('admin_member_form_old_input_session_key')) {
    function admin_member_form_old_input_session_key()
    {
        return 'ss_admin_member_form_old_input';
    }
}

function admin_store_member_form_old_input($w, array $request)
{
    $old_input = $request;
    $old_input['mb_password'] = '';

    set_session(admin_member_form_old_input_session_key(), array(
        'w' => $w,
        'mb_no' => isset($request['mb_no']) ? (int) $request['mb_no'] : 0,
        'mb_id' => isset($request['mb_id']) ? (string) $request['mb_id'] : '',
        'request' => $old_input,
    ));
}

function admin_clear_member_form_old_input()
{
    set_session(admin_member_form_old_input_session_key(), '');
}

function admin_complete_member_form_update_request(array $update_request, array $member, $is_admin, $auth, $sub_menu)
{
    $w = $update_request['form']['w'];
    $request = $update_request['member'];

    admin_validate_member_form_update_action($w);

    auth_check_menu($auth, $sub_menu, 'w');
    check_admin_token();

    admin_store_member_form_old_input($w, $request);
    $mb_id = admin_persist_member_form_request($w, $request, $member, $is_admin);
    admin_clear_member_form_old_input();

    if (function_exists('get_admin_captcha_by')) {
        get_admin_captcha_by('remove');
    }

    run_event('admin_member_form_update', $w, $mb_id);
    admin_set_flash_message('success', '저장완료');
    goto_url(admin_build_member_form_update_redirect($update_request['list_qstr'], $mb_id), false);
}
