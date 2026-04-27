<?php
// 회원관리 메뉴 정의 파일이다. 메뉴 rendering은 menu-bootstrap/ui-shell 파일에서 처리한다.
admin_register_menu_group($menu, 'menu200', array(
    array('200000', '회원관리', G5_ADMIN_URL . '/member_list.php', 'member'),
    array('200100', '회원관리', G5_ADMIN_URL . '/member_list.php', 'mb_list'),
    array('200400', '회원관리파일', G5_ADMIN_URL . '/member_list_exel.php', 'mb_list'),
));
