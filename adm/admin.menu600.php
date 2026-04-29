<?php
// 커뮤니티 메뉴 정의 파일이다. 메뉴 rendering은 menu-bootstrap/ui-shell 파일에서 처리한다.
admin_register_menu_group($menu, 'menu600', array(
    array('600000', '커뮤니티', G5_ADMIN_URL . '/community_board_list.php', 'community'),
    array('600050', '기본환경 설정', G5_ADMIN_URL . '/community_config_form.php', 'community_config'),
    array('600075', '게시판 그룹', G5_ADMIN_URL . '/community_group_list.php', 'community_group'),
    array('600100', '게시판 관리', G5_ADMIN_URL . '/community_board_list.php', 'community_board'),
    array('600200', '게시글 관리', G5_ADMIN_URL . '/community_post_list.php', 'community_post'),
    array('600300', '댓글 관리', G5_ADMIN_URL . '/community_comment_list.php', 'community_comment'),
    array('600500', '포인트 관리', G5_ADMIN_URL . '/community_point_list.php', 'community_point'),
));
