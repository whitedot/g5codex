<?php
// 커뮤니티 메뉴 정의 파일이다. 메뉴 rendering은 menu-bootstrap/ui-shell 파일에서 처리한다.
admin_register_menu_group($menu, 'menu300', array(
    array('300000', '커뮤니티', G5_ADMIN_URL . '/community_board_list.php', 'community'),
    array('300050', '기본환경 설정', G5_ADMIN_URL . '/community_config_form.php', 'community_config'),
    array('300075', '게시판 그룹', G5_ADMIN_URL . '/community_group_list.php', 'community_group'),
    array('300100', '게시판 관리', G5_ADMIN_URL . '/community_board_list.php', 'community_board'),
    array('300200', '게시글 관리', G5_ADMIN_URL . '/community_post_list.php', 'community_post'),
    array('300300', '댓글 관리', G5_ADMIN_URL . '/community_comment_list.php', 'community_comment'),
    array('300500', '포인트 관리', G5_ADMIN_URL . '/community_point_list.php', 'community_point'),
    array('300600', '커뮤니티 점검', G5_ADMIN_URL . '/community_health.php', 'community_health'),
));
