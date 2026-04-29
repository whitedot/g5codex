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

<div class="member-search-card">
    <form method="get" action="<?php echo $community_point_view['search_action_attr']; ?>">
        <div class="member-search-fields community-search-fields community-search-fields-compact">
            <div class="member-field">
                <label for="point_mb_id" class="member-field-label">회원 ID</label>
                <input type="text" name="mb_id" value="<?php echo $community_point_view['mb_id_value']; ?>" id="point_mb_id" class="form-input member-field-input" placeholder="회원 ID">
            </div>
            <button type="submit" class="btn btn-solid-primary member-search-submit">검색</button>
        </div>
    </form>
</div>

<form method="post" action="<?php echo $community_point_view['adjust_action_attr']; ?>" class="admin-form-layout ui-form-theme ui-form-showcase community-point-adjust-form">
    <input type="hidden" name="token" value="<?php echo $community_point_view['admin_token']; ?>">
    <input type="hidden" name="return_query" value="<?php echo $community_point_view['return_query_attr']; ?>">
    <section class="card">
        <div class="card-header">
            <h2 class="card-title">포인트 조정</h2>
        </div>
        <div class="card-body">
            <div class="af-grid">
                <div class="af-row">
                    <div class="af-label">
                        <label for="adjust_mb_id" class="form-label">회원 ID<strong class="caption-sr-only">필수</strong></label>
                    </div>
                    <div class="af-field">
                        <input type="text" name="mb_id" id="adjust_mb_id" value="<?php echo $community_point_view['mb_id_value']; ?>" class="form-input" required>
                    </div>
                </div>

                <div class="af-row">
                    <div class="af-label">
                        <label for="adjust_amount" class="form-label">조정 포인트<strong class="caption-sr-only">필수</strong></label>
                    </div>
                    <div class="af-field">
                        <input type="number" name="amount" id="adjust_amount" class="form-input" required>
                        <p class="hint-text">지급은 양수, 차감은 음수로 입력합니다.</p>
                    </div>
                </div>

                <div class="af-row">
                    <div class="af-label">
                        <label for="adjust_memo" class="form-label">메모</label>
                    </div>
                    <div class="af-field">
                        <input type="text" name="memo" id="adjust_memo" class="form-input" maxlength="255" placeholder="조정 사유">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="admin-form-actions admin-form-actions-primary community-point-adjust-actions">
        <button type="submit" class="btn btn-solid-primary">포인트 조정</button>
    </div>
</form>

<h2 class="h2_frm">지갑</h2>
<div class="member-table-card community-table-card">
    <div class="table-wrapper">
        <table class="table community-list-table">
            <caption>커뮤니티 포인트 지갑</caption>
            <thead class="ui-table-head">
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
                    <td><strong class="community-id"><?php echo $item['mb_id_text']; ?></strong></td>
                    <td class="community-number"><?php echo $item['balance_text']; ?></td>
                    <td class="community-number is-positive"><?php echo $item['earned_text']; ?></td>
                    <td class="community-number is-negative"><?php echo $item['spent_text']; ?></td>
                    <td class="community-number is-muted"><?php echo $item['expired_text']; ?></td>
                    <td class="community-date"><?php echo $item['updated_at_text']; ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($community_point_view['wallet_items'])) { ?>
                <tr><td colspan="6" class="ui-table-empty"><?php echo $community_point_view['empty_wallet_message']; ?></td></tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo $community_point_view['paging_html']; ?>

<h2 class="h2_frm">최근 원장</h2>
<div class="member-table-card community-table-card">
    <div class="table-wrapper">
        <table class="table community-list-table">
            <caption>커뮤니티 포인트 원장</caption>
            <thead class="ui-table-head">
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
                    <td><strong class="community-id"><?php echo $item['mb_id_text']; ?></strong></td>
                    <td class="community-number <?php echo $item['amount_class']; ?>"><?php echo $item['amount_text']; ?></td>
                    <td class="community-number"><?php echo $item['balance_after_text']; ?></td>
                    <td><?php echo $item['reason_text']; ?></td>
                    <td><?php echo $item['target_text']; ?></td>
                    <td><?php echo $item['created_by_text']; ?></td>
                    <td class="community-date"><?php echo $item['created_at_text']; ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($community_point_view['ledger_items'])) { ?>
                <tr><td colspan="8" class="ui-table-empty"><?php echo $community_point_view['empty_ledger_message']; ?></td></tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
