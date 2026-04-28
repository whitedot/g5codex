<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 관리자 shell의 meta/link/script/menu/header/footer view-model을 담당한다.
// 화면별 title/container/subtitle은 admin_apply_page_view()가 설정한 전역 상태를 입력으로 받는다.

function admin_menu_icon_id($menu_code)
{
    $prefix = substr((string) $menu_code, 0, 3);
    $map = array(
        '100' => 'settings',
        '200' => 'users',
        '300' => 'content',
        '400' => 'folder',
        '500' => 'stats',
        '900' => 'message',
    );

    return isset($map[$prefix]) ? $map[$prefix] : 'folder';
}

function admin_menu_is_readable(array $auth, $is_admin, $menu_code)
{
    return $is_admin == 'super' || (array_key_exists($menu_code, $auth) && strstr($auth[$menu_code], 'r'));
}

function admin_build_head_link_view($rel, $href, $as = '', $crossorigin = false)
{
    $rel_attr = admin_escape_attr($rel);
    $href_attr = admin_escape_attr($href);
    $as_html_attr = $as !== '' ? ' as="' . admin_escape_attr($as) . '"' : '';
    $crossorigin_attr = $crossorigin ? ' crossorigin' : '';

    return array(
        'tag_html' => '<link rel="' . $rel_attr . '"' . $as_html_attr . ' href="' . $href_attr . '"' . $crossorigin_attr . '>',
    );
}

function admin_build_head_meta_view($name, $content, $id = '')
{
    $id_html_attr = $id !== '' ? ' id="' . admin_escape_attr($id) . '"' : '';

    return array(
        'tag_html' => '<meta name="' . admin_escape_attr($name) . '"' . $id_html_attr . ' content="' . admin_escape_attr($content) . '">',
    );
}

function admin_build_head_javascript_view($src, $priority = 0)
{
    return array(
        'tag_html' => '<script src="' . admin_escape_attr($src) . '"></script>',
        'priority' => $priority,
    );
}

function admin_build_head_submenu_items(array $menu_group, array $auth, $is_admin, $sub_menu)
{
    $submenu_items = array();

    foreach ($menu_group as $index => $menu_item) {
        if ($index === 0 || !isset($menu_item[0], $menu_item[1], $menu_item[2])) {
            continue;
        }

        if (!admin_menu_is_readable($auth, $is_admin, $menu_item[0])) {
            continue;
        }

        $is_current = ((string) $menu_item[0] === (string) $sub_menu);
        $submenu_items[] = array(
            'menu_code_attr' => admin_escape_attr($menu_item[0]),
            'title_text' => get_text((string) $menu_item[1]),
            'href_attr' => admin_escape_attr($menu_item[2]),
            'item_class_attr' => $is_current ? ' is-current' : '',
        );
    }

    return $submenu_items;
}

function admin_build_head_navigation(array $amenu, array $menu, array $auth, $is_admin, $sub_menu)
{
    $navigation_items = array();

    foreach ($amenu as $key => $value) {
        $group_key = 'menu' . $key;
        if (!isset($menu[$group_key][0][0], $menu[$group_key][0][1], $menu[$group_key][0][2]) || !$menu[$group_key][0][2]) {
            continue;
        }

        $menu_group = $menu[$group_key];
        $menu_code = (string) $menu_group[0][0];
        $submenu_items = admin_build_head_submenu_items($menu_group, $auth, $is_admin, $sub_menu);

        $is_open = (substr((string) $sub_menu, 0, 3) === substr($menu_code, 0, 3));

        $navigation_items[] = array(
            'title_text' => get_text((string) $menu_group[0][1]),
            'title_attr' => admin_escape_attr($menu_group[0][1]),
            'icon_id_attr' => admin_escape_attr(admin_menu_icon_id($menu_code)),
            'item_class_attr' => $is_open ? ' is-open' : '',
            'panel_class_attr' => $is_open ? '' : ' hidden',
            'aria_expanded_attr' => $is_open ? 'true' : 'false',
            'sub_items' => $submenu_items,
        );
    }

    return $navigation_items;
}

function admin_build_head_view(array $member, array $config, array $cookies, $admin_container_class = '', $admin_page_subtitle = '', array $amenu = array(), array $menu = array(), array $auth = array(), $is_admin = '', $sub_menu = '', $admin_page_title = '')
{
    $admin_sidebar_container_class = '';
    $admin_sidebar_class = '';
    $admin_sidebar_toggle_class = '';

    if (!empty($cookies['g5_admin_btn_gnb'])) {
        $admin_sidebar_container_class = 'container-small';
        $admin_sidebar_class = 'gnb_small';
        $admin_sidebar_toggle_class = 'btn_gnb_open';
    }

    $admin_profile_raw_id = (string) (isset($member['mb_id']) ? $member['mb_id'] : '');
    $admin_site_title = get_text((string) (isset($config['cf_title']) ? $config['cf_title'] : ''));
    if ($admin_site_title === '') {
        $admin_site_title = 'G5 AIF';
    }

    return array(
        'admin_sidebar_class_attr' => admin_escape_attr($admin_sidebar_class),
        'admin_sidebar_toggle_class_attr' => admin_escape_attr($admin_sidebar_toggle_class),
        'admin_profile_manage_url_attr' => admin_escape_attr(G5_ADMIN_URL . '/member_form.php?w=u&amp;mb_id=' . rawurlencode($admin_profile_raw_id)),
        'admin_logout_url_attr' => admin_escape_attr(G5_MEMBER_URL . '/logout.php'),
        'admin_dashboard_url_attr' => admin_escape_attr(correct_goto_url(G5_ADMIN_URL)),
        'admin_site_home_url_attr' => admin_escape_attr(G5_URL . '/'),
        'admin_site_title_text' => $admin_site_title,
        'admin_csrf_token_key_json' => admin_json_string(function_exists('admin_csrf_token_key') ? admin_csrf_token_key() : ''),
        'admin_navigation_items' => admin_build_head_navigation($amenu, $menu, $auth, $is_admin, $sub_menu),
        'admin_container_class_attr' => admin_escape_attr(trim($admin_sidebar_container_class . ' ' . $admin_container_class)),
        'admin_page_title_text' => get_text($admin_page_title),
        'admin_page_subtitle_text' => get_text($admin_page_subtitle !== '' ? $admin_page_subtitle : '사이트 운영과 설정을 한 곳에서 관리하세요.'),
    );
}

function admin_build_tail_view($is_admin)
{
    $admin_script_files = array(
        'admin-core.js',
        'admin-config-form.js',
        'admin-member-export.js',
        'admin-member-form.js',
        'admin-member-list.js',
        'admin-shell.js',
        'admin.js',
    );
    $admin_script_tag_views = array();
    foreach ($admin_script_files as $script_file) {
        $script_path = G5_ADMIN_PATH . '/' . $script_file;
        $script_src = G5_ADMIN_URL . '/' . $script_file . '?ver=' . (is_file($script_path) ? filemtime($script_path) : G5_JS_VER);
        $admin_script_tag_views[] = array(
            'tag_html' => '<script src="' . admin_escape_attr($script_src) . '"></script>',
        );
    }

    $server_input = function_exists('g5_get_runtime_server_input') ? g5_get_runtime_server_input() : array();
    $host = isset($server_input['HTTP_HOST']) ? get_text((string) $server_input['HTTP_HOST']) : '';

    return array(
        'copyright_host_text' => $host,
        'script_tag_views' => $admin_script_tag_views,
    );
}

function admin_build_head_sub_view(array $g5, array $config, $is_member, $is_admin, array $member)
{
    $page_title = isset($g5['title']) ? $g5['title'] : '';
    if ($page_title === '') {
        $page_title = $config['cf_title'];
        $head_title = $page_title;
    } else {
        $head_title = implode(' | ', array_filter(array($page_title, $config['cf_title'])));
    }

    $page_title = strip_tags($page_title);
    $head_title = strip_tags($head_title);
    $pretendard_font_href = 'https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css';
    $common_css_ver = admin_file_version(G5_PATH . '/css/common.css', G5_CSS_VER);
    $admin_css_ver = admin_file_version(G5_ADMIN_PATH . '/css/admin.css', G5_CSS_VER);
    $sticky_anchor_tabs_ver = admin_file_version(G5_PATH . '/js/ui-kit/ui-sticky-anchor-tabs.js', G5_JS_VER);
    $common_css_href = run_replace('head_css_url', G5_CSS_URL . '/common.css?ver=' . $common_css_ver, G5_URL);
    $admin_css_href = run_replace('head_css_url', G5_ADMIN_URL . '/css/admin.css?ver=' . $admin_css_ver, G5_URL);

    $login_status_text = '';
    $login_status_html = '';
    if ($is_member) {
        $sr_admin_msg = $is_admin == 'super' ? '최고관리자 ' : '';
        $login_status_text = $sr_admin_msg . get_text($member['mb_nick']) . '님 로그인 중 ';
        $login_status_html = '<div class="sr-only">' . get_text($login_status_text) . '<a href="' . admin_escape_attr(G5_MEMBER_URL . '/logout.php') . '">로그아웃</a></div>';
    }

    $g5_sca = '';
    if (isset($g5['request_context']['query_state']['sca']) && !is_array($g5['request_context']['query_state']['sca'])) {
        $g5_sca = (string) $g5['request_context']['query_state']['sca'];
    }

    $js_global_values = array(
        'g5_url' => G5_URL,
        'g5_member_url' => G5_MEMBER_URL,
        'g5_is_member' => isset($is_member) ? $is_member : '',
        'g5_is_admin' => isset($is_admin) ? $is_admin : '',
        'g5_is_mobile' => G5_IS_MOBILE,
        'g5_sca' => $g5_sca,
        'g5_cookie_domain' => G5_COOKIE_DOMAIN,
        'g5_admin_url' => G5_ADMIN_URL,
    );
    $js_global_views = array();
    foreach ($js_global_values as $name => $value) {
        $js_global_views[] = array(
            'name_attr' => admin_escape_attr($name),
            'value_json' => admin_json_string($value),
        );
    }

    $head_javascript_views = array(
        admin_build_head_javascript_view(G5_JS_URL . '/common.js?ver=' . G5_JS_VER, 0),
        admin_build_head_javascript_view(G5_JS_URL . '/ui-kit/ui-dropdown-menu.js?ver=' . G5_JS_VER, 1),
        admin_build_head_javascript_view(G5_JS_URL . '/ui-kit/ui-sticky-anchor-tabs.js?ver=' . $sticky_anchor_tabs_ver, 1),
        admin_build_head_javascript_view(G5_JS_URL . '/wrest.js?ver=' . G5_JS_VER, 0),
    );

    $mobile_meta_views = array();
    if (G5_IS_MOBILE) {
        $mobile_meta_views[] = admin_build_head_meta_view('viewport', 'width=device-width,initial-scale=1.0', 'meta_viewport');
        $mobile_meta_views[] = admin_build_head_meta_view('format-detection', 'telephone=no');
    }

    $head_link_views = array(
        admin_build_head_link_view('preconnect', 'https://cdn.jsdelivr.net', '', true),
        admin_build_head_link_view('preload', $pretendard_font_href, 'style', true),
        admin_build_head_link_view('stylesheet', $pretendard_font_href, '', true),
        admin_build_head_link_view('stylesheet', $common_css_href),
        admin_build_head_link_view('stylesheet', $admin_css_href),
    );

    return array(
        'page_title_text' => $page_title,
        'head_title_text' => $head_title,
        'mobile_meta_views' => $mobile_meta_views,
        'head_link_views' => $head_link_views,
        'head_javascript_views' => $head_javascript_views,
        'login_status_html' => $login_status_html,
        'body_script' => isset($g5['body_script']) ? $g5['body_script'] : '',
        'js_global_views' => $js_global_views,
    );
}
