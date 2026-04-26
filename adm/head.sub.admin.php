<?php
// 이 파일은 새로운 파일 생성시 반드시 포함되어야 함
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$g5_debug['php']['begin_time'] = $begin_time = get_microtime();

$head_sub_view = admin_build_head_sub_view($g5, $config, $is_member, $is_admin, $member);
$page_title_text = $head_sub_view['page_title_text'];
$head_title_text = $head_sub_view['head_title_text'];
$mobile_meta_views = $head_sub_view['mobile_meta_views'];
$head_link_views = $head_sub_view['head_link_views'];
$head_javascript_views = $head_sub_view['head_javascript_views'];
$login_status_html = $head_sub_view['login_status_html'];
$body_script = $head_sub_view['body_script'];
$js_global_views = $head_sub_view['js_global_views'];
$g5['title'] = $page_title_text;
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<script>
(function () {
    var key = 'g5_admin_theme';
    var saved = null;
    try {
        saved = localStorage.getItem(key);
    } catch (e) {
        saved = null;
    }
    var dark = saved === 'dark' || (!saved && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
})();
</script>
<?php
foreach ($mobile_meta_views as $mobile_meta_view) {
    echo $mobile_meta_view['tag_html'].PHP_EOL;
}
?>
<title><?php echo $head_title_text; ?></title>
<?php
foreach ($head_link_views as $head_link_view) {
    echo $head_link_view['tag_html'].PHP_EOL;
}
?>
<script>
// 자바스크립트에서 사용하는 전역변수 선언
<?php foreach ($js_global_views as $js_global) { ?>
var <?php echo $js_global['name_attr']; ?> = <?php echo $js_global['value_json']; ?>;
<?php } ?>
</script>
<?php
foreach ($head_javascript_views as $javascript_view) {
    add_javascript($javascript_view['tag_html'], $javascript_view['priority']);
}
?>
</head>
<body<?php echo $body_script; ?>>
<?php echo $login_status_html; ?>
