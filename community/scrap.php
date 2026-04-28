<?php
include_once './_common.php';

if (empty($member['mb_id'])) {
    alert('로그인 후 이용해 주십시오.', G5_COMMUNITY_URL);
}

$community_scrap_request = community_read_scrap_list_request(g5_get_runtime_get_input(), $config);
$community_scrap_view = community_build_scrap_list_view($community_scrap_request, $member, $is_admin);
$g5['title'] = $community_scrap_view['title'];

include_once G5_PATH . '/head.php';
include_once G5_COMMUNITY_VIEW_PATH . '/basic/scrap.skin.php';
include_once G5_PATH . '/tail.php';
