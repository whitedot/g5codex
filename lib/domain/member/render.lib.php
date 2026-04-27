<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 회원 render aggregate loader다. include 선언만 유지한다.
// template include, view helper, 응답 보조, page view 조립은 render-*.lib.php에서 담당한다.

require_once __DIR__ . '/render-template.lib.php';
require_once __DIR__ . '/render-view.lib.php';
require_once __DIR__ . '/render-response.lib.php';
require_once __DIR__ . '/render-register-form.lib.php';
require_once __DIR__ . '/render-page-view.lib.php';
