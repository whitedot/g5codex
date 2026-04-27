<?php
// 검증 지도: 관리자 AJAX 토큰 발급 controller다.
// 요청 정규화와 응답 종료는 token.lib.php에서 담당한다.
// 이 파일에는 토큰 생성 규칙이나 JSON 출력 로직을 직접 추가하지 않는다.
require_once './_common.php';

$request = admin_read_ajax_token_request(g5_get_runtime_post_input());
admin_complete_ajax_token_request($request);
