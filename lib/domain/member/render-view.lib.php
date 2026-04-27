<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 화면 출력용 escape/json helper를 담당한다.
// 업무 상태 계산은 render-page-view.lib.php 또는 flow 파일에서 끝난 값을 받는다.

function member_json_string($value)
{
    return json_encode((string) $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function member_escape_attr($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

class MemberViewRenderer
{
    public static function capture($view_path, $template_name, array $data = array())
    {
        return MemberTemplateRenderer::capture(rtrim($view_path, '/\\') . '/' . $template_name, $data);
    }

    public static function display($view_path, $template_name, array $data = array())
    {
        echo self::capture($view_path, $template_name, $data);
    }
}

class MemberMailRenderer
{
    public static function capture($template_name, array $data = array())
    {
        return MemberTemplateRenderer::capture(G5_MEMBER_VIEW_PATH . '/mail/' . $template_name, $data);
    }
}
