<?php
require_once './_common.php';

$slug = isset($_GET['slug']) && !is_array($_GET['slug']) ? $_GET['slug'] : '';
$site_page = site_fetch_page_by_slug($slug, G5_IS_MOBILE ? 'mobile' : 'pc', $member);

if (empty($site_page['page_id'])) {
    alert('존재하지 않는 페이지입니다.', G5_URL);
}

$g5['title'] = $site_page['title'];
require_once G5_PATH . '/head.php';
?>

<article class="site-page-view">
    <header class="site-page-header">
        <h2><?php echo get_text($site_page['title']); ?></h2>
        <?php if ($site_page['summary'] !== '') { ?>
        <p><?php echo get_text($site_page['summary']); ?></p>
        <?php } ?>
    </header>
    <div class="site-page-content">
        <?php echo site_render_page_content($site_page); ?>
    </div>
</article>

<?php
require_once G5_PATH . '/tail.php';
