<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

include_once G5_LIB_PATH . '/cache.lib.php';

function community_cache_normalize_key($key)
{
    return preg_replace('/[^a-zA-Z0-9:_-]/', '_', (string) $key);
}

function community_cache_get($key)
{
    if (!function_exists('g5_get_cache')) {
        return false;
    }

    return g5_get_cache(community_cache_normalize_key($key));
}

function community_cache_set($key, $value, $ttl = 60)
{
    if (!function_exists('g5_set_cache')) {
        return false;
    }

    g5_set_cache(community_cache_normalize_key($key), $value, (int) $ttl);
    return true;
}

function community_cache_delete($key)
{
    if (!function_exists('g5_delete_cache')) {
        return false;
    }

    return g5_delete_cache(community_cache_normalize_key($key));
}

function community_cache_delete_group($group)
{
    if (!function_exists('g5_delete_cache_by_prefix')) {
        return false;
    }

    return g5_delete_cache_by_prefix(community_cache_normalize_key($group));
}
