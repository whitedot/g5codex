<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 자동 POST form과 짧은 response rendering helper를 담당한다.
// 업무 판단과 redirect 목적지는 flow 파일에서 결정한 값을 받는다.

class MemberResponseRenderer
{
    private static function buildAutoPostFieldViews(array $fields)
    {
        $field_views = array();

        foreach ($fields as $name => $value) {
            $field_views[] = array(
                'name_attr' => member_escape_attr($name),
                'value_attr' => member_escape_attr($value),
            );
        }

        return $field_views;
    }

    public static function alertScript($message)
    {
        if ($message === null || $message === '') {
            return;
        }

        echo '<script>alert(' . member_json_string($message) . ');</script>';
    }

    public static function autoPost($action, array $fields, $message = '', $title = '처리중')
    {
        echo MemberTemplateRenderer::capture(
            G5_MEMBER_VIEW_PATH . '/basic/auto_post_form.skin.php',
            array(
                'action_attr' => member_escape_attr($action),
                'fields' => self::buildAutoPostFieldViews($fields),
                'message_json' => $message !== '' ? member_json_string($message) : '',
                'title_text' => member_escape_attr($title),
            )
        );
        exit;
    }
}
