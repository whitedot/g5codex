<?php
include_once './_common.php';

if (empty($member['mb_id'])) {
    alert('로그인 후 이용해 주십시오.', G5_COMMUNITY_URL);
}

$community_point_request = community_read_point_list_request(g5_get_runtime_get_input(), $config);
$community_point_view = community_build_point_view($community_point_request, $member);
$g5['title'] = $community_point_view['title'];

include_once G5_PATH . '/head.php';
include_once G5_COMMUNITY_VIEW_PATH . '/basic/point.skin.php';
include_once G5_PATH . '/tail.php';
