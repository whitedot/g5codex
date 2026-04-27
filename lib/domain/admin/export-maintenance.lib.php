<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 export 임시 파일 수동 정리 action과 결과 화면 view-model을 담당한다.
// 실제 파일 삭제 helper는 export-file-cleanup.lib.php에서 처리한다.

function admin_delete_directory_tree($folder_path)
{
    $items = glob($folder_path . '/*');
    if (!is_array($items)) {
        $items = array();
    }

    foreach ($items as $item) {
        if (is_dir($item)) {
            admin_delete_directory_tree($item);
            continue;
        }

        @unlink($item);
    }

    @rmdir($folder_path);
}

function admin_process_member_list_file_delete()
{
    $base_path = G5_DATA_PATH . '/member_list';
    $messages = array();
    $count = 0;

    if (!is_dir($base_path)) {
        return array(
            'messages' => array('회원관리파일을 열지 못했습니다.'),
            'count' => 0,
        );
    }

    $files = glob($base_path . '/*');
    if (!is_array($files)) {
        $files = array();
    }

    foreach ($files as $member_list_file) {
        $ext = strtolower(pathinfo($member_list_file, PATHINFO_EXTENSION));
        $basename = basename($member_list_file);

        if (is_file($member_list_file) && $ext !== 'log') {
            @unlink($member_list_file);
            $messages[] = '파일 삭제: ' . $member_list_file;
            $count++;
            continue;
        }

        if (is_dir($member_list_file) && $basename !== 'log') {
            admin_delete_directory_tree($member_list_file);
            $messages[] = '폴더 삭제: ' . $member_list_file;
            $count++;
        }
    }

    $messages[] = '완료됨';

    return array(
        'messages' => $messages,
        'count' => $count,
    );
}

function admin_build_member_list_file_delete_result_view(array $delete_result)
{
    $messages = array();
    $raw_messages = isset($delete_result['messages']) && is_array($delete_result['messages']) ? $delete_result['messages'] : array();

    foreach ($raw_messages as $message) {
        $messages[] = get_text((string) $message);
    }

    return array(
        'messages' => $messages,
        'count' => isset($delete_result['count']) ? (int) $delete_result['count'] : 0,
    );
}

function admin_complete_member_list_file_delete_request($is_admin)
{
    admin_require_super_admin($is_admin);
    $delete_result = admin_process_member_list_file_delete();

    return array(
        'title' => '회원관리파일 일괄삭제',
        'admin_container_class' => 'admin-page-member-export-delete',
        'admin_page_subtitle' => '서버에 남아 있는 회원 내보내기 산출물을 정리하고 삭제 결과를 바로 확인하세요.',
        'result' => admin_build_member_list_file_delete_result_view($delete_result),
    );
}
