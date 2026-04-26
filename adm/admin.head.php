<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

$g5_debug['php']['begin_time'] = $begin_time = get_microtime();

admin_enqueue_extend_stylesheets();

require_once G5_ADMIN_PATH . '/head.sub.admin.php';

$admin_head_view = admin_build_head_view(
    $member,
    $config,
    g5_get_runtime_cookie_input(),
    isset($admin_container_class) ? $admin_container_class : '',
    isset($admin_page_subtitle) ? $admin_page_subtitle : '',
    isset($amenu) && is_array($amenu) ? $amenu : array(),
    isset($menu) && is_array($menu) ? $menu : array(),
    isset($auth) && is_array($auth) ? $auth : array(),
    isset($is_admin) ? $is_admin : '',
    isset($sub_menu) ? $sub_menu : '',
    isset($page_title_text) ? $page_title_text : ''
);
?>

<script>
    var g5_admin_csrf_token_key = <?php echo $admin_head_view['admin_csrf_token_key_json']; ?>;
</script>

<div id="to_content" class="admin-skip-link"><a href="#container">본문 바로가기</a></div>

<header id="hd" class="admin-sidebar-frame">
    <h1 class="sr-only"><?php echo $admin_head_view['admin_site_title_text']; ?></h1>

    <nav id="gnb" class="admin-sidebar <?php echo $admin_head_view['admin_sidebar_class_attr']; ?>" aria-label="관리자 메뉴">
        <svg class="admin-nav-icon-sprite" aria-hidden="true" focusable="false">
            <symbol id="admin-menu-icon-settings" viewBox="0 0 24 24">
                <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065"></path>
                <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
            </symbol>
            <symbol id="admin-menu-icon-admin-mode" viewBox="0 0 24 24">
                <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"></path>
                <path d="M11 11a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                <path d="M12 12l0 2.5"></path>
            </symbol>
            <symbol id="admin-menu-icon-users" viewBox="0 0 24 24">
                <path d="M5 7a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
            </symbol>
            <symbol id="admin-menu-icon-user" viewBox="0 0 24 24">
                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
            </symbol>
            <symbol id="admin-menu-icon-content" viewBox="0 0 24 24">
                <path d="M5 4h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1"></path>
                <path d="M5 16h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1"></path>
                <path d="M15 12h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1"></path>
                <path d="M15 4h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1"></path>
            </symbol>
            <symbol id="admin-menu-icon-stats" viewBox="0 0 24 24">
                <path d="M3 13a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -6"></path>
                <path d="M15 9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -10"></path>
                <path d="M9 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -14"></path>
                <path d="M4 20h14"></path>
            </symbol>
            <symbol id="admin-menu-icon-message" viewBox="0 0 24 24">
                <path d="M3 20l1.3 -3.9c-2.324 -3.437 -1.426 -7.872 2.1 -10.374c3.526 -2.501 8.59 -2.296 11.845 .48c3.255 2.777 3.695 7.266 1.029 10.501c-2.666 3.235 -7.615 4.215 -11.574 2.293l-4.7 1"></path>
            </symbol>
            <symbol id="admin-menu-icon-article" viewBox="0 0 24 24">
                <path d="M3 6a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2l0 -12"></path>
                <path d="M7 8h10"></path>
                <path d="M7 12h10"></path>
                <path d="M7 16h10"></path>
            </symbol>
            <symbol id="admin-menu-icon-home" viewBox="0 0 24 24">
                <path d="M5 12l-2 0l9 -9l9 9l-2 0"></path>
                <path d="M5 12v7a2 2 0 0 0 2 2h3m4 0h3a2 2 0 0 0 2 -2v-7"></path>
                <path d="M10 12h4v9h-4z"></path>
            </symbol>
            <symbol id="admin-menu-icon-logout" viewBox="0 0 24 24">
                <path d="M14 8v-2a2 2 0 0 0 -2 -2h-5a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5a2 2 0 0 0 2 -2v-2"></path>
                <path d="M9 12h12l-3 -3"></path>
                <path d="M18 15l3 -3"></path>
            </symbol>
            <symbol id="admin-menu-icon-folder" viewBox="0 0 24 24">
                <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"></path>
            </symbol>
            <symbol id="admin-menu-icon-sidebar-toggle" viewBox="0 0 24 24">
                <path d="M4 6a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2l0 -12"></path>
                <path d="M9 4v16"></path>
                <path d="M15 10l-2 2l2 2"></path>
            </symbol>
            <symbol id="admin-menu-icon-menu" viewBox="0 0 24 24">
                <path d="M4 6l16 0"></path>
                <path d="M4 12l16 0"></path>
                <path d="M4 18l16 0"></path>
            </symbol>
            <symbol id="admin-menu-icon-moon-stars" viewBox="0 0 24 24">
                <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454l0 .008"></path>
                <path d="M17 4a2 2 0 0 0 2 2a2 2 0 0 0 -2 2a2 2 0 0 0 -2 -2a2 2 0 0 0 2 -2"></path>
                <path d="M19 11h2m-1 -1v2"></path>
            </symbol>
            <symbol id="admin-menu-icon-sun" viewBox="0 0 24 24">
                <path d="M8 12a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7"></path>
            </symbol>
            <symbol id="admin-menu-icon-chevron-down" viewBox="0 0 24 24">
                <path d="M6 9l6 6l6 -6"></path>
            </symbol>
        </svg>

        <h2 class="admin-sidebar-brand">
            <a class="admin-sidebar-brand-link" href="<?php echo $admin_head_view['admin_dashboard_url_attr']; ?>">
                <span class="admin-sidebar-brand-mark" aria-hidden="true">
                    <svg class="admin-shell-control-icon" focusable="false" viewBox="0 0 24 24">
                        <use href="#admin-menu-icon-admin-mode"></use>
                    </svg>
                </span>
                <span class="admin-sidebar-brand-name"><?php echo $admin_head_view['admin_site_title_text']; ?></span>
            </a>
            <button type="button" id="btn_gnb" class="admin-sidebar-toggle <?php echo $admin_head_view['admin_sidebar_toggle_class_attr']; ?>" aria-label="사이드바 축소/확장" aria-pressed="false">
                <span aria-hidden="true">
                    <svg class="admin-shell-control-icon" focusable="false" viewBox="0 0 24 24">
                        <use href="#admin-menu-icon-sidebar-toggle"></use>
                    </svg>
                </span>
            </button>
        </h2>

        <div class="gnb_menu_scroll_wrap admin-sidebar-scroll-wrap">
            <div class="gnb_menu_scroll admin-sidebar-scroll" id="gnbMenuScroll">
                <ul class="admin-nav-list" id="adminNavList">
                    <?php foreach ($admin_head_view['admin_navigation_items'] as $nav_item) { ?>
                        <li class="admin-nav-item<?php echo $nav_item['item_class_attr']; ?>">
                            <button type="button" class="admin-nav-trigger" title="<?php echo $nav_item['title_attr']; ?>" aria-expanded="<?php echo $nav_item['aria_expanded_attr']; ?>">
                                <span class="admin-nav-trigger-main">
                                    <svg class="admin-nav-icon" aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                                        <use href="#admin-menu-icon-<?php echo $nav_item['icon_id_attr']; ?>"></use>
                                    </svg>
                                    <span class="admin-nav-trigger-label"><?php echo $nav_item['title_text']; ?></span>
                                </span>
                                <span class="admin-nav-caret" aria-hidden="true">
                                    <svg class="admin-nav-caret-icon" focusable="false" viewBox="0 0 24 24">
                                        <use href="#admin-menu-icon-chevron-down"></use>
                                    </svg>
                                </span>
                            </button>
                            <div class="admin-nav-panel<?php echo $nav_item['panel_class_attr']; ?>">
                                <ul class="admin-nav-sub-list">
                                    <?php foreach ($nav_item['sub_items'] as $sub_item) { ?>
                                        <li class="admin-nav-sub-item<?php echo $sub_item['item_class_attr']; ?>" data-menu="<?php echo $sub_item['menu_code_attr']; ?>">
                                            <a href="<?php echo $sub_item['href_attr']; ?>"><?php echo $sub_item['title_text']; ?></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="gnb_scrollbar admin-sidebar-scrollbar" aria-hidden="true">
                <div class="gnb_scrollbar_thumb admin-sidebar-scrollbar-thumb"></div>
            </div>
        </div>

        <div class="gnb_profile admin-sidebar-profile">
            <div class="gnb_profile_avatar admin-sidebar-profile-avatar"><?php echo $admin_head_view['admin_profile_initial_text']; ?></div>
            <div class="gnb_profile_meta admin-sidebar-profile-meta">
                <strong><?php echo $admin_head_view['admin_profile_display_name_text']; ?></strong>
                <span><?php echo $admin_head_view['admin_profile_mail_text']; ?></span>
            </div>
            <a class="gnb_profile_logout admin-sidebar-profile-logout" href="<?php echo $admin_head_view['admin_logout_url_attr']; ?>" title="로그아웃" aria-label="로그아웃">
                <svg class="admin-shell-control-icon" aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                    <use href="#admin-menu-icon-logout"></use>
                </svg>
            </a>
        </div>
    </nav>

    <div id="adminSidebarBackdrop" class="admin-sidebar-backdrop hidden"></div>
</header>

<div id="wrapper" class="admin-wrapper">
    <div id="hd_top" class="admin-topbar">
        <div class="hd_top_left admin-topbar-left">
            <button type="button" id="btn_gnb_mobile" class="admin-mobile-menu-button" aria-controls="gnb" aria-expanded="false" aria-label="메뉴 열기">
                <svg class="admin-shell-control-icon" aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                    <use href="#admin-menu-icon-menu"></use>
                </svg>
            </button>
            <div class="hd_breadcrumb admin-breadcrumb">
                <span>대시보드</span>
                <span>/</span>
                <strong><?php echo $admin_head_view['admin_page_title_text']; ?></strong>
            </div>
        </div>

        <div class="hd_top_right admin-topbar-right">
            <div id="tnb" class="admin-toolbar">
                <ul>
                    <li class="tnb_li admin-toolbar-item">
                        <button type="button" id="admin_theme_toggle" class="tnb_icon_btn admin-toolbar-icon-button" aria-pressed="false" aria-label="다크 모드 전환" title="다크 모드 전환">
                            <svg class="admin-shell-control-icon" aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                                <use id="admin_theme_toggle_icon_use" href="#admin-menu-icon-moon-stars"></use>
                            </svg>
                        </button>
                    </li>
                    <li class="tnb_li admin-toolbar-item">
                        <a class="tnb_icon_btn admin-toolbar-icon-button" href="<?php echo $admin_head_view['admin_site_home_url_attr']; ?>" target="_blank" title="메인" aria-label="메인">
                            <svg class="admin-shell-control-icon" aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                                <use href="#admin-menu-icon-home"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="tnb_li admin-toolbar-item relative">
                        <button type="button" class="tnb_mb_btn tnb_icon_btn admin-toolbar-icon-button" aria-label="관리자 메뉴" title="관리자 메뉴">
                            <svg class="admin-shell-control-icon" aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                                <use href="#admin-menu-icon-user"></use>
                            </svg>
                        </button>
                        <ul class="tnb_mb_area admin-toolbar-menu hidden">
                            <li><a href="<?php echo $admin_head_view['admin_profile_manage_url_attr']; ?>">관리자정보</a></li>
                            <li id="tnb_logout"><a href="<?php echo $admin_head_view['admin_logout_url_attr']; ?>">로그아웃</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div id="container" class="admin-content <?php echo $admin_head_view['admin_container_class_attr']; ?>">
        <h1 id="container_title" class="admin-content-title"><?php echo $admin_head_view['admin_page_title_text']; ?></h1>
        <p id="container_subtitle" class="admin-content-subtitle"><?php echo $admin_head_view['admin_page_subtitle_text']; ?></p>
