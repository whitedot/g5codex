<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<section class="community-point">
    <header>
        <h2><?php echo get_text($g5['title']); ?></h2>
    </header>

    <div class="community-point-summary">
        <div>
            <span>현재 포인트</span>
            <strong><?php echo $community_point_view['balance_text']; ?></strong>
        </div>
        <div>
            <span>사용 가능</span>
            <strong><?php echo $community_point_view['available_total_text']; ?></strong>
        </div>
        <div>
            <span>적립 합계</span>
            <strong><?php echo $community_point_view['earned_total_text']; ?></strong>
        </div>
        <div>
            <span>사용 합계</span>
            <strong><?php echo $community_point_view['spent_total_text']; ?></strong>
        </div>
        <div>
            <span>만료 합계</span>
            <strong><?php echo $community_point_view['expired_total_text']; ?></strong>
        </div>
    </div>

    <div class="community-point-expiry">
        <p>만료 대상 <?php echo $community_point_view['expiring_total_text']; ?>점 · 만료 없음 <?php echo $community_point_view['non_expiring_total_text']; ?>점 · 다음 만료 <?php echo $community_point_view['next_expires_at_text']; ?></p>
    </div>

    <?php if (empty($community_point_view['items'])) { ?>
        <p><?php echo $community_point_view['empty_message']; ?></p>
    <?php } else { ?>
        <div class="community-point-table-wrap">
            <table class="community-point-table">
                <caption>내 포인트 내역</caption>
                <thead>
                <tr>
                    <th scope="col">일시</th>
                    <th scope="col">사유</th>
                    <th scope="col">포인트</th>
                    <th scope="col">잔액</th>
                    <th scope="col">만료</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($community_point_view['items'] as $item) { ?>
                    <tr>
                        <td><?php echo $item['created_at_text']; ?></td>
                        <td>
                            <strong><?php echo $item['reason_text']; ?></strong>
                            <?php if ($item['target_text'] !== '') { ?><span><?php echo $item['target_text']; ?></span><?php } ?>
                        </td>
                        <td class="<?php echo $item['amount_class']; ?>"><?php echo $item['amount_text']; ?></td>
                        <td><?php echo $item['balance_after_text']; ?></td>
                        <td><?php echo $item['expires_at_text']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

    <?php echo $community_point_view['paging_html']; ?>
</section>
