<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// XLSX aggregate loader다. include 선언만 유지한다.
// ZIP archive helper와 Spreadsheet XML writer는 각각 archive.lib.php, xlsx-writer.lib.php에서 담당한다.
require_once __DIR__ . '/archive.lib.php';
require_once __DIR__ . '/xlsx-writer.lib.php';
