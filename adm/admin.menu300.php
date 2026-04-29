<?php
// 메뉴 관리 메뉴 정의 파일이다. 메뉴 rendering은 menu-bootstrap/ui-shell 파일에서 처리한다.
admin_register_menu_group($menu, 'menu300', array(
    array('300000', '메뉴 관리', G5_ADMIN_URL . '/site_menu_list.php', 'site_menu'),
    array('300100', '전체 메뉴 관리', G5_ADMIN_URL . '/site_menu_list.php', 'site_menu'),
));
