<?php // 회원 등록/수정 연락처 partial이다. 주소/연락처 view-model은 member-form-view.lib.php에서 준비한다. ?>
<section id="anc_mb_contact" class="card">
    <div class="card-header">
        <h2 class="card-title">인증 연락처</h2>
    </div>
    <div class="card-body">
        <div class="af-grid">
            <div class="af-row">
                <div class="af-label">
                    <label for="mb_hp" class="form-label">휴대폰번호</label>
                </div>
                <div class="af-field">
                    <input type="text" name="mb_hp" value="<?php echo $contact_view['member']['mb_hp'] ?>" id="mb_hp" size="15" maxlength="20" class="form-input">
                </div>
            </div>

            <div class="af-row">
                <div class="af-label">
                    <label for="mb_certify_sa" class="form-label">본인확인 방법</label>
                </div>
                <div class="af-field">
                    <div class="af-inline">
                        <?php foreach ($contact_view['certify_case_options'] as $option) { ?>
                        <label for="<?php echo $option['id_attr']; ?>" class="af-check form-label">
                            <input type="radio" name="mb_certify_case" value="<?php echo $option['value_attr']; ?>" id="<?php echo $option['id_attr']; ?>"<?php echo $option['checked_attr']; ?> class="form-radio">
                            <span class="form-label"><?php echo $option['label_text']; ?></span>
                        </label>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="af-row">
                <div class="af-label">
                    <label for="mb_certify_yes" class="form-label">본인확인</label>
                </div>
                <div class="af-field">
                    <div class="af-inline">
                        <?php foreach ($contact_view['mb_certify_options'] as $option) { ?>
                        <label for="<?php echo $option['id_attr']; ?>" class="af-check form-label">
                            <input type="radio" name="mb_certify" value="<?php echo $option['value_attr']; ?>" id="<?php echo $option['id_attr']; ?>"<?php echo $option['checked_attr']; ?> class="form-radio">
                            <span class="form-label"><?php echo $option['label_text']; ?></span>
                        </label>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="af-row">
                <div class="af-label">
                    <label for="mb_adult_yes" class="form-label">성인인증</label>
                </div>
                <div class="af-field">
                    <div class="af-inline">
                        <?php foreach ($contact_view['mb_adult_options'] as $option) { ?>
                        <label for="<?php echo $option['id_attr']; ?>" class="af-check form-label">
                            <input type="radio" name="mb_adult" value="<?php echo $option['value_attr']; ?>" id="<?php echo $option['id_attr']; ?>"<?php echo $option['checked_attr']; ?> class="form-radio">
                            <span class="form-label"><?php echo $option['label_text']; ?></span>
                        </label>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
