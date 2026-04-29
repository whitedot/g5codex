<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

run_event('pre_head');

include_once(G5_PATH.'/head.sub.php');

$head_community_point_balance_text = '';
if ($is_member && function_exists('community_point_refresh_member_wallet')) {
    $head_community_point_wallet = community_point_refresh_member_wallet($member['mb_id'], true);
    $head_community_point_balance_text = number_format((int) $head_community_point_wallet['balance']);
}

$head_site_menu_items = function_exists('site_fetch_menu_tree') ? site_fetch_menu_tree(G5_IS_MOBILE ? 'mobile' : 'pc', $member) : array();
?>

<div id="hd">
    <h1 id="hd_h1"><?php echo $g5['title'] ?></h1>
    <div id="skip_to_container"><a href="#container">본문 바로가기</a></div>

    <div id="tnb">
        <div>
            <ul id="hd_define">
                <li class="active"><a href="<?php echo G5_URL ?>/">회원 서비스</a></li>
                <li><a href="<?php echo G5_COMMUNITY_URL ?>/">커뮤니티</a></li>
            </ul>
        </div>
    </div>
    <div id="hd_wrapper">
        <div id="logo">
            <a href="<?php echo G5_URL ?>">
                <?php if (is_file(G5_IMG_PATH.'/logo.png')) { ?>
                <img src="<?php echo G5_IMG_URL ?>/logo.png" alt="<?php echo $config['cf_title']; ?>">
                <?php } else { ?>
                <span><?php echo get_text($config['cf_title']); ?></span>
                <?php } ?>
            </a>
        </div>

        <div></div>
        <ul class="hd_login">
            <?php if ($is_member) { ?>
            <li><a href="<?php echo G5_COMMUNITY_URL ?>/point.php">포인트 <?php echo $head_community_point_balance_text; ?></a></li>
            <li><a href="<?php echo G5_MEMBER_URL ?>/member_confirm.php?url=<?php echo G5_MEMBER_URL ?>/register_form.php">정보수정</a></li>
            <li><a href="<?php echo G5_MEMBER_URL ?>/member_confirm.php?url=member_leave.php">회원탈퇴</a></li>
            <li><a href="<?php echo G5_MEMBER_URL ?>/logout.php">로그아웃</a></li>
            <?php if ($is_admin) { ?>
            <li><a href="<?php echo correct_goto_url(G5_ADMIN_URL); ?>">관리자</a></li>
            <?php } ?>
            <?php } else { ?>
            <li><a href="<?php echo G5_MEMBER_URL ?>/register.php">회원가입</a></li>
            <li><a href="<?php echo G5_MEMBER_URL ?>/login.php">로그인</a></li>
            <?php } ?>
        </ul>
    </div>

    <nav id="gnb">
        <h2>메인메뉴</h2>
        <div>
            <ul id="gnb_1dul">
                <?php if (!empty($head_site_menu_items)) { ?>
                <?php foreach ($head_site_menu_items as $head_site_menu_item) { ?>
                <li>
                    <a href="<?php echo community_escape_attr($head_site_menu_item['url']); ?>"<?php echo !empty($head_site_menu_item['target_blank']) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo get_text($head_site_menu_item['name']); ?></a>
                    <?php if (!empty($head_site_menu_item['children'])) { ?>
                    <ul>
                        <?php foreach ($head_site_menu_item['children'] as $head_site_menu_child) { ?>
                        <li><a href="<?php echo community_escape_attr($head_site_menu_child['url']); ?>"<?php echo !empty($head_site_menu_child['target_blank']) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo get_text($head_site_menu_child['name']); ?></a></li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                </li>
                <?php } ?>
                <?php } else { ?>
                <li><a href="<?php echo G5_URL ?>/">홈</a></li>
                <li><a href="<?php echo G5_COMMUNITY_URL ?>/">커뮤니티</a></li>
                <?php if ($is_member) { ?>
                <li><a href="<?php echo G5_COMMUNITY_URL ?>/point.php">내 포인트</a></li>
                <li><a href="<?php echo G5_MEMBER_URL ?>/member_confirm.php?url=<?php echo G5_MEMBER_URL ?>/register_form.php">내 정보</a></li>
                <?php } ?>
                <?php } ?>
            </ul>
        </div>
    </nav>
</div>

<hr>

<div id="wrapper">
    <div id="container_wr">
        <div id="container">
            <?php if (!defined('_INDEX_')) { ?><h2 id="container_title"><span title="<?php echo get_text($g5['title']); ?>"><?php echo get_head_title($g5['title']); ?></span></h2><?php } ?>
