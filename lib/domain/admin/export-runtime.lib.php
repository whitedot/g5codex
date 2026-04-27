<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 export 실행에 필요한 런타임 context만 만든다.
// 화면 출력값은 export-view.lib.php, SSE 다운로드 실행은 export-stream.lib.php를 확인한다.
// 이 파일에 HTML 출력이나 파일 생성 로직을 추가하지 않는다.

function admin_member_export_supports_xlsx()
{
    return admin_archive_supports_zip();
}

function admin_member_export_runtime_error_message()
{
    if (admin_member_export_supports_xlsx()) {
        return '';
    }

    return '압축 파일 생성 환경이 준비되지 않아 회원 엑셀 내보내기를 실행할 수 없습니다. 서버 PHP 설정과 파일 쓰기 권한을 확인한 뒤 다시 시도해 주세요.';
}

function admin_build_member_export_runtime_context(array $tables, array $member_row = array())
{
    return array(
        'member_table' => isset($tables['member_table']) ? $tables['member_table'] : '',
        'actor_id' => isset($member_row['mb_id']) ? $member_row['mb_id'] : 'guest',
        'environment_ready' => admin_member_export_supports_xlsx(),
        'environment_error' => admin_member_export_runtime_error_message(),
    );
}

function admin_build_member_export_page_request(array $query, array $config, array $tables, array $member_row = array())
{
    $runtime = admin_build_member_export_runtime_context($tables, $member_row);

    return array(
        'runtime' => $runtime,
        'view' => admin_build_member_export_page_view($query, $config, $runtime),
    );
}
