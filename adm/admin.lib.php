<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 관리자 domain 진입점이다. 순수 helper/security를 먼저 로드한 뒤 bootstrap side effect를 실행한다.
// 개별 화면 업무 로직은 adm/*.php controller와 lib/domain/admin/*.lib.php 파일에서 처리한다.
require_once G5_LIB_PATH . '/domain/admin/helper.lib.php';
require_once G5_LIB_PATH . '/domain/admin/security.lib.php';
require_once G5_LIB_PATH . '/domain/admin/bootstrap.lib.php';
