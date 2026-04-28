<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<div class="member-summary">
    <div class="member-summary-stats">
        <span class="member-summary-meta">총 지갑 <strong><?php echo $community_point_view['total_count_text']; ?></strong></span>
    </div>
    <form method="post" action="<?php echo $community_point_view['expire_action_attr']; ?>">
        <input type="hidden" name="token" value="<?php echo $community_point_view['admin_token']; ?>">
        <input type="hidden" name="return_query" value="<?php echo $community_point_view['return_query_attr']; ?>">
        <button type="submit" class="btn btn-solid-secondary">만료 포인트 정산</button>
    </form>
</div>

<form method="get" action="<?php echo $community_point_view['search_action_attr']; ?>" class="member-search">
    <div class="member-search-fields">
        <label for="point_mb_id" class="member-field-label">회원 ID</label>
        <input type="text" name="mb_id" value="<?php echo $community_point_view['mb_id_value']; ?>" id="point_mb_id" class="form-input member-field-input" placeholder="회원 ID">
        <button type="submit" class="btn btn-solid-primary">검색</button>
    </div>
</form>

<form method="post" action="<?php echo $community_point_view['adjust_action_attr']; ?>" class="admin-form-layout ui-form-theme">
    <input type="hidden" name="token" value="<?php echo $community_point_view['admin_token']; ?>">
    <input type="hidden" name="return_query" value="<?php echo $community_point_view['return_query_attr']; ?>">
    <div class="tbl_frm01 tbl_wrap">
        <table>
            <tbody>
            <tr>
                <th scope="row"><label for="adjust_mb_id">회원 ID</label></th>
                <td><input type="text" name="mb_id" id="adjust_mb_id" value="<?php echo $community_point_view['mb_id_value']; ?>" class="frm_input" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="adjust_amount">조정 포인트</label></th>
                <td><input type="number" name="amount" id="adjust_amount" class="frm_input" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="adjust_memo">메모</label></th>
                <td><input type="text" name="memo" id="adjust_memo" class="frm_input" maxlength="255"></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="btn_fixed_top">
        <button type="submit" class="btn btn-solid-primary">포인트 조정</button>
    </div>
</form>

<h2 class="h2_frm">지갑</h2>
<div class="tbl_head01 tbl_wrap">
    <table>
        <caption>커뮤니티 포인트 지갑</caption>
        <thead>
        <tr>
            <th scope="col">회원 ID</th>
            <th scope="col">잔액</th>
            <th scope="col">적립</th>
            <th scope="col">사용</th>
            <th scope="col">만료</th>
            <th scope="col">갱신일</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($community_point_view['wallet_items'] as $item) { ?>
            <tr>
                <td><?php echo $item['mb_id_text']; ?></td>
                <td><?php echo $item['balance_text']; ?></td>
                <td><?php echo $item['earned_text']; ?></td>
                <td><?php echo $item['spent_text']; ?></td>
                <td><?php echo $item['expired_text']; ?></td>
                <td><?php echo $item['updated_at_text']; ?></td>
            </tr>
        <?php } ?>
        <?php if (empty($community_point_view['wallet_items'])) { ?>
            <tr><td colspan="6" class="ui-table-empty"><?php echo $community_point_view['empty_wallet_message']; ?></td></tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php echo $community_point_view['paging_html']; ?>

<h2 class="h2_frm">최근 원장</h2>
<div class="tbl_head01 tbl_wrap">
    <table>
        <caption>커뮤니티 포인트 원장</caption>
        <thead>
        <tr>
            <th scope="col">번호</th>
            <th scope="col">회원 ID</th>
            <th scope="col">금액</th>
            <th scope="col">잔액</th>
            <th scope="col">사유</th>
            <th scope="col">대상</th>
            <th scope="col">처리자</th>
            <th scope="col">일시</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($community_point_view['ledger_items'] as $item) { ?>
            <tr>
                <td><?php echo $item['ledger_id_text']; ?></td>
                <td><?php echo $item['mb_id_text']; ?></td>
                <td><?php echo $item['amount_text']; ?></td>
                <td><?php echo $item['balance_after_text']; ?></td>
                <td><?php echo $item['reason_text']; ?></td>
                <td><?php echo $item['target_text']; ?></td>
                <td><?php echo $item['created_by_text']; ?></td>
                <td><?php echo $item['created_at_text']; ?></td>
            </tr>
        <?php } ?>
        <?php if (empty($community_point_view['ledger_items'])) { ?>
            <tr><td colspan="8" class="ui-table-empty"><?php echo $community_point_view['empty_ledger_message']; ?></td></tr>
        <?php } ?>
        </tbody>
    </table>
</div>
