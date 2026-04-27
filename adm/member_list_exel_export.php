<?php
// 검증 지도: 관리자 회원 export SSE 다운로드 controller다.
// runtime context는 export-runtime.lib.php, stream 검증/진행 이벤트는 export-stream.lib.php,
// 파일 생성/ZIP/정리는 export-file*.lib.php를 확인한다.
$sub_menu = '200400';
require_once './_common.php';

$page_request = admin_build_member_export_stream_page_request(g5_get_runtime_get_input(), $g5, isset($member) && is_array($member) ? $member : array());
admin_complete_member_export_stream_page($page_request, $auth, $sub_menu);
