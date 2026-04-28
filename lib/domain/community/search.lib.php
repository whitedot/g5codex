<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_search_normalize_keyword($keyword)
{
    return trim(strip_tags((string) $keyword));
}

function community_search_apply_post_list_condition(array $request, array &$where, array &$params)
{
    $keyword = community_search_normalize_keyword(isset($request['stx']) ? $request['stx'] : '');

    if ($keyword === '') {
        return;
    }

    $where[] = '(title like :stx_like or mb_id like :stx_like)';
    $params['stx_like'] = '%' . $keyword . '%';
}
