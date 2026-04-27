<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 view hook include helper를 담당한다.
// 화면별 업무 상태는 hook 호출 전에 controller/domain flow에서 준비한다.

class MemberViewHookController
{
    public static function includeOptional($view_path, $template_name)
    {
        $template_path = rtrim($view_path, '/\\') . '/' . $template_name;

        if (is_file($template_path)) {
            include_once($template_path);
        }
    }
}
