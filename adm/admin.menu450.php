<?php
// 배너 관리 메뉴 정의 파일이다. 메뉴 rendering은 menu-bootstrap/ui-shell 파일에서 처리한다.
admin_register_menu_group($menu, 'menu450', array(
    array('450000', '배너 관리', G5_ADMIN_URL . '/site_banner_list.php', 'site_banner'),
    array('450100', '전체 배너 관리', G5_ADMIN_URL . '/site_banner_list.php', 'site_banner'),
));
