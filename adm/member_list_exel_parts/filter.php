<form id="fsearch" name="fsearch" method="get" class="card ui-form-theme ui-form-showcase" data-admin-member-export-form>
    <input type="hidden" name="token" value="<?php echo $member_export_filter_view['form_token_attr']; ?>">
    <div class="card-header">
        <h2 class="card-title">회원 검색 필터링</h2>
    </div>
    <fieldset class="card-body">
        <legend class="caption-sr-only">회원 검색 필터링</legend>
        <div class="af-grid">
            <div class="af-row">
                <div class="af-label">
                    <label class="af-check form-label">
                        <input type="checkbox" name="use_stx" value="1" <?php echo $member_export_filter_view['use_stx_checked_attr']; ?> class="form-checkbox">
                        <span class="form-label">검색어 적용</span>
                    </label>
                </div>
                <div class="af-field">
                    <div class="admin-export-search-line">
                        <select name="sfl" class="form-select admin-export-select-auto">
                            <?php foreach ($member_export_filter_view['sfl_option_items'] as $option) { ?>
                                <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                            <?php } ?>
                        </select>
                        <input type="text" name="stx" value="<?php echo $member_export_filter_view['stx_value_attr']; ?>" placeholder="검색어 입력" class="form-input admin-export-search-keyword">
                        <div class="af-inline admin-export-search-options">
                            <label class="af-check form-label">
                                <input type="radio" name="stx_cond" value="like" <?php echo $member_export_filter_view['stx_cond_like_checked_attr']; ?> class="form-radio">
                                <span class="form-label">포함</span>
                            </label>
                            <label class="af-check form-label">
                                <input type="radio" name="stx_cond" value="equal" <?php echo $member_export_filter_view['stx_cond_equal_checked_attr']; ?> class="form-radio">
                                <span class="form-label">일치</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="af-row">
                <div class="af-label">
                    <label class="af-check form-label">
                        <input type="checkbox" name="use_level" value="1" <?php echo $member_export_filter_view['use_level_checked_attr']; ?> class="form-checkbox">
                        <span class="form-label">레벨 적용</span>
                    </label>
                </div>
                <div class="af-field">
                    <div class="af-inline">
                        <select name="level_start" class="form-select">
                            <?php foreach ($member_export_filter_view['level_start_options'] as $option) { ?>
                                <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                            <?php } ?>
                        </select>
                        <span class="ui-form-inline-note">~</span>
                        <select name="level_end" class="form-select">
                            <?php foreach ($member_export_filter_view['level_end_options'] as $option) { ?>
                                <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="af-row">
                <div class="af-label">
                    <label class="af-check form-label">
                        <input type="checkbox" name="use_date" value="1" <?php echo $member_export_filter_view['use_date_checked_attr']; ?> class="form-checkbox">
                        <span class="form-label">가입기간 적용</span>
                    </label>
                </div>
                <div class="af-field">
                    <div class="af-inline">
                        <input type="date" name="date_start" max="9999-12-31" value="<?php echo $member_export_filter_view['date_start_value_attr']; ?>" class="form-input">
                        <span class="ui-form-inline-note">~</span>
                        <input type="date" name="date_end" max="9999-12-31" value="<?php echo $member_export_filter_view['date_end_value_attr']; ?>" class="form-input">
                    </div>
                </div>
            </div>

            <div class="af-row">
                <div class="af-label">
                    <label class="af-check form-label">
                        <input type="checkbox" name="use_intercept" value="1" <?php echo $member_export_filter_view['use_intercept_checked_attr']; ?> class="form-checkbox">
                        <span class="form-label">차단회원</span>
                    </label>
                </div>
                <div class="af-field">
                    <select name="intercept" id="intercept" class="form-select admin-export-select-auto">
                        <?php foreach ($member_export_filter_view['intercept_option_items'] as $option) { ?>
                            <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="af-row">
                <div class="af-label">
                    <label class="af-check form-label">
                        <input type="checkbox" name="use_hp_exist" value="1" <?php echo $member_export_filter_view['use_hp_checked_attr']; ?> class="form-checkbox">
                        <span class="form-label">휴대폰 번호 있는 경우만</span>
                    </label>
                </div>
                <div class="af-field">
                    <p class="hint-text">휴대폰 번호가 입력된 회원만 내보냅니다.</p>
                </div>
            </div>

            <div class="af-row">
                <div class="af-label">
                    <label class="af-check form-label">
                        <input type="checkbox" name="ad_range_only" value="1" <?php echo $member_export_filter_view['ad_range_only_checked_attr']; ?> class="form-checkbox">
                        <span class="form-label">정보수신동의에 동의한 경우만</span>
                    </label>
                </div>
                <div class="af-field">
                    <p class="hint-text">「정보통신망이용촉진및정보보호등에관한법률」에 따라 <strong>광고성 정보 수신동의 여부</strong>를 <strong>매2년</strong>마다 확인해야 합니다.</p>
                </div>
            </div>

            <div class="af-row ad_range_wrap <?php echo $member_export_filter_view['ad_range_wrap_class_attr']; ?>">
                <div class="af-label">
                    <label for="ad_range_type" class="form-label">회원범위</label>
                </div>
                <div class="af-field">
                    <div class="admin-export-filter-stack">
                        <select name="ad_range_type" id="ad_range_type" class="form-select admin-export-select-auto">
                            <?php foreach ($member_export_filter_view['ad_range_option_items'] as $option) { ?>
                                <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                            <?php } ?>
                        </select>

                        <div class="ad_range_wrap admin-export-filter-stack">
                            <div class="<?php echo $member_export_filter_view['custom_period_class_attr']; ?> admin-export-custom-period" data-admin-member-export-custom-period>
                                <div class="af-inline">
                                    <input type="date" name="agree_date_start" max="9999-12-31" value="<?php echo $member_export_filter_view['agree_date_start_value_attr']; ?>" class="form-input">
                                    <span class="ui-form-inline-note">~</span>
                                    <input type="date" name="agree_date_end" max="9999-12-31" value="<?php echo $member_export_filter_view['agree_date_end_value_attr']; ?>" class="form-input">
                                </div>
                                <p class="hint-text">광고성 정보 수신(<strong>이메일</strong>) 동의일자 기준</p>
                            </div>

                            <?php if ($member_export_filter_view['active_ad_range_html'] !== '') { ?>
                                <div><p class="hint-text hint-text-flush"><?php echo $member_export_filter_view['active_ad_range_html']; ?></p></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="af-row ad_range_wrap <?php echo $member_export_filter_view['channel_row_class_attr']; ?>" data-admin-member-export-channel-row>
                <div class="af-label">
                    <label class="form-label">확인 채널</label>
                </div>
                <div class="af-field">
                    <div class="af-inline">
                        <label class="af-check form-label"><input type="checkbox" name="ad_mailling" value="1" <?php echo $member_export_filter_view['ad_mailling_checked_attr']; ?> class="form-checkbox"><span class="form-label">광고성 이메일 수신</span></label>
                    </div>
                </div>
            </div>

            <div class="admin-export-filter-actions">
                <button type="button" id="btnExcelDownload" class="btn btn-solid-primary" <?php echo $member_export_filter_view['download_disabled_attr']; ?>>엑셀파일 다운로드</button>
                <a href="<?php echo $member_export_filter_view['reset_url_attr']; ?>" class="btn btn-surface-default-soft">초기화</a>
            </div>
        </div>
    </fieldset>
</form>
