<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_member_level(array $member)
{
    return isset($member['mb_level']) ? (int) $member['mb_level'] : 1;
}

function community_can_read_board(array $board, array $member)
{
    return community_member_level($member) >= (int) $board['read_level'];
}

function community_can_write_board(array $board, array $member)
{
    return isset($member['mb_id']) && $member['mb_id'] !== '' && community_member_level($member) >= (int) $board['write_level'];
}

function community_can_comment_board(array $board, array $member)
{
    return !empty($board['use_comment']) && isset($member['mb_id']) && $member['mb_id'] !== '' && community_member_level($member) >= (int) $board['comment_level'];
}

function community_can_edit_post(array $post, array $member, $is_admin)
{
    if ($is_admin) {
        return true;
    }

    return isset($member['mb_id']) && $member['mb_id'] !== '' && $post['mb_id'] === $member['mb_id'];
}

function community_can_view_secret_post(array $post, array $member, $is_admin)
{
    if (empty($post['is_secret'])) {
        return true;
    }

    return community_can_edit_post($post, $member, $is_admin);
}

function community_can_edit_comment(array $comment, array $member, $is_admin)
{
    if ($is_admin) {
        return true;
    }

    return isset($member['mb_id']) && $member['mb_id'] !== '' && $comment['mb_id'] === $member['mb_id'];
}
