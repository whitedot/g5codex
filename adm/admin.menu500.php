<?php
// 페이지 관리 메뉴 정의 파일이다. 메뉴 rendering은 menu-bootstrap/ui-shell 파일에서 처리한다.
admin_register_menu_group($menu, 'menu500', array(
    array('500000', '페이지 관리', G5_ADMIN_URL . '/site_page_list.php', 'site_page'),
    array('500100', '전체 페이지 관리', G5_ADMIN_URL . '/site_page_list.php', 'site_page'),
));
