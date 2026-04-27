<?php // 회원 등록/수정 프로필/메모 partial이다. 이 파일은 화면 출력만 담당하고 저장 로직은 갖지 않는다. ?>
<section id="anc_mb_profile" class="card">
    <div class="card-header">
        <h2 class="card-title">관리 메모</h2>
    </div>
    <div class="card-body">
        <div class="af-grid">
            <div class="af-row">
                <div class="af-label">
                    <label for="mb_memo" class="form-label">메모</label>
                </div>
                <div class="af-field">
                    <textarea name="mb_memo" id="mb_memo" class="form-textarea"><?php echo $profile_view['memo_value']; ?></textarea>
                </div>
            </div>
        </div>
    </div>
</section>
