<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function site_register_runtime_tables()
{
    global $g5;

    if (!defined('G5_TABLE_PREFIX')) {
        return;
    }

    if (empty($g5['site_page_table'])) {
        $g5['site_page_table'] = G5_TABLE_PREFIX . 'site_page';
    }
}

site_register_runtime_tables();
