<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 skin/template capture helper를 담당한다.
// 템플릿에 전달할 data 배열은 render-page-view/register-form 파일에서 준비한다.

class MemberTemplateRenderer
{
    public static function capture($template_path, array $data = array())
    {
        global $g5, $config, $member, $is_member, $is_admin;

        if (!is_file($template_path)) {
            return '';
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $template_path;
        return ob_get_clean();
    }

    public static function display($template_path, array $data = array())
    {
        echo self::capture($template_path, $data);
    }
}
