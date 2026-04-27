<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 AJAX 중복 검사 요청 정규화를 담당한다.
// 실제 중복 검사와 짧은 응답 종료는 validation-ajax.lib.php 및 flow-ajax.lib.php에서 처리한다.

function member_read_ajax_identity_request(array $post)
{
    return array(
        'mb_id' => isset($post['reg_mb_id']) ? trim($post['reg_mb_id']) : '',
        'mb_email' => isset($post['reg_mb_email']) ? trim($post['reg_mb_email']) : '',
        'mb_hp' => isset($post['reg_mb_hp']) ? trim($post['reg_mb_hp']) : '',
        'mb_nick' => isset($post['reg_mb_nick']) ? trim($post['reg_mb_nick']) : '',
    );
}
