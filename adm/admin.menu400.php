<?php
// 배너 관리 메뉴 정의 파일이다. 메뉴 rendering은 menu-bootstrap/ui-shell 파일에서 처리한다.
admin_register_menu_group($menu, 'menu400', array(
    array('400000', '배너 관리', G5_ADMIN_URL . '/site_banner_list.php', 'site_banner'),
    array('400100', '전체 배너 관리', G5_ADMIN_URL . '/site_banner_list.php', 'site_banner'),
));
