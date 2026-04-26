<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

$admin_tail_view = admin_build_tail_view($is_admin);
?>

<noscript>
    <p>
        이 페이지는 JavaScript가 활성화되어야 일부 기능이 정상 동작합니다.
    </p>
</noscript>
    </div>

    <footer id="ft" class="admin-footer">
        <p class="admin-footer-inner">
            <span class="admin-footer-copy">Copyright &copy; <?php echo $admin_tail_view['copyright_host_text']; ?>. All rights reserved. <?php echo $admin_tail_view['print_version_text']; ?></span>
            <button type="button" class="admin-footer-scroll-top"><span>TOP</span></button>
        </p>
    </footer>
</div>

<div id="adminPopupContainer" class="admin-popup-container">
    <div id="popupOverlay" class="admin-popup-overlay is-hidden hidden">
        <div class="admin-popup-dialog">
            <div class="admin-popup-header">
                <strong id="popupTitle" class="admin-popup-title"></strong>
                <button type="button" class="admin-popup-close" data-popup-close="popupOverlay">
                    <i></i><span>팝업 닫기</span>
                </button>
            </div>
            <div id="popupBody" class="admin-popup-body"></div>
            <div id="popupFooter" class="admin-popup-footer"></div>
        </div>
    </div>
</div>

<?php foreach ($admin_tail_view['script_tag_views'] as $script_tag_view) { ?>
<?php echo $script_tag_view['tag_html'].PHP_EOL; ?>
<?php } ?>

<?php
require_once G5_PATH . '/tail.sub.php';
