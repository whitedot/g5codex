<div class="member-search-card">
    <form id="fsearch" name="fsearch" method="get">
        <div class="member-search-fields">
            <div class="member-field">
                <label for="sfl" class="member-field-label">검색대상</label>
                <select name="sfl" id="sfl" class="form-select member-field-input">
                    <?php foreach ($member_list_view['search_view']['field_options'] as $option) { ?>
                        <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="member-field">
                <label for="stx" class="member-field-label">검색어</label>
                <input type="text" name="stx" value="<?php echo $member_list_view['search_view']['stx_value']; ?>" id="stx" required class="required form-input member-field-input" placeholder="검색어를 입력하세요">
            </div>
            <button type="submit" class="btn btn-solid-primary member-search-submit">검색</button>
        </div>
    </form>
</div>
